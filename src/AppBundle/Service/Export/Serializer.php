<?php

namespace AppBundle\Service\Export;

use DateTime;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\YamlEncoder;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
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
        $data = $this->getDataToSerialize($entity);
        $data = $this->setSerializer()->serialize($data, $format, ['groups' => ['export']]);

        $this->exportProvider->export($data, $format, $entity);
    }

    private function getDataToSerialize(string $entityName): array
    {
        $isEntityEndsWithS = 's' === substr($entityName, -1);
        $collectionKeyName = sprintf('%s%s', strtolower($entityName), $isEntityEndsWithS ? 'es' : 's');
        $dateTime = new DateTime();
        $data = [
//            'created_at' => $saveDateTime = sprintf('Data utworzenia exportu: %s', $dateTime->format('H:i:s d.m.Y')),
//            $collectionKeyName => [],
        ];

        $entityArray = $this->chooseEntityToExport($entityName);
        foreach ($entityArray as $entity) {
            $data[] = $entity;
        }

        return $data;
    }

    private function chooseEntityToExport(string $entity): array
    {
        return $this->entityManager->getRepository(sprintf('AppBundle\Entity\%s', ucfirst($entity)))->findAll();
    }

    private function setSerializer(): SymfonySerializer
    {
        $encoders = [new XmlEncoder(), new JsonEncoder(), new CsvEncoder(), new YamlEncoder()];
        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $normalizer = new ObjectNormalizer($classMetadataFactory, new CamelCaseToSnakeCaseNameConverter());
        $normalizers = array(new DateTimeNormalizer(), $normalizer);

        return new SymfonySerializer($normalizers, $encoders);
    }
}
