<?php

namespace AppBundle\Service\Export;

use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\YamlEncoder;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer as SymfonySerializer;

/**
 * @author Jacek Wesołowski <jacqu25@yahoo.com>
 */
class Serializer
{
    private $entityManager;

    private $exportProvider;

    public function __construct(EntityManagerInterface $entityManager, ExportProvider $exportProvider)
    {
        $this->entityManager = $entityManager;
        $this->exportProvider = $exportProvider;
    }

    public function serialize(string $entity, string $format): void
    {
        $data = $this->getDataToSerialize($entity)['data'];
        $fieldsToSkip = $this->getDataToSerialize($entity)['fieldsToSkip'];
        $data = $this->setSerializer($fieldsToSkip)->serialize($data, $format);

        $this->exportProvider->export($data, $format, $entity);
    }

    private function getDataToSerialize(string $entityName): array
    {
        $dateTime = new DateTime();
        $collectionKeyName = sprintf('%ss', strtolower($entityName));
        $data = [
            'created_at' => $saveDateTime = sprintf('Data utworzenia exportu: %s', $dateTime->format('H:i:s d.m.Y')),
            $collectionKeyName => [],
        ];

        $entityArray = $this->chooseEntityToExport($entityName);
        $fieldsToSkip = [];
        foreach ($entityArray as $entity) {
            if (empty($entity->isPropertyCollection())) {
                $fieldsToSkip = [];
            } else {
                $fieldsToSkip = $entity->isPropertyCollection();
            }

            $data[$collectionKeyName][] = $entity;
        }

        return [
            'data' => $data,
            'fieldsToSkip' => $fieldsToSkip
        ];
    }

    private function chooseEntityToExport(string $entity): array
    {
        return $this->entityManager->getRepository(sprintf('AppBundle\Entity\%s', ucfirst($entity)))->findAll();
    }

    private function setSerializer(array $fieldsToSkip): SymfonySerializer
    {
        $encoders = [new XmlEncoder(), new JsonEncoder(), new CsvEncoder(), new YamlEncoder()];
        $normalizer = new ObjectNormalizer(null, new CamelCaseToSnakeCaseNameConverter());
        $normalizer->setIgnoredAttributes($fieldsToSkip);
        $normalizers = array($normalizer);

        return new SymfonySerializer($normalizers, $encoders);
    }
}
