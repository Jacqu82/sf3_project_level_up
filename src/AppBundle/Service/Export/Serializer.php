<?php

namespace AppBundle\Service\Export;

use DateTime;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
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

    public function serialize(string $entity, string $format, bool $backToImport, ?int $id): void
    {
        $data = $this->getDataToSerialize($entity, $backToImport, $id);
        $data = $this->setSerializer()->serialize($data, $format, ['groups' => ['export']]);

        $this->exportProvider->export($data, $format, $entity, $backToImport, $id);
    }

    /**
     * @param string $entityName
     * @param bool $backToImport
     * @param int|null $id
     *
     * @return array|object
     *
     * @throws Exception
     */
    private function getDataToSerialize(string $entityName, bool $backToImport, ?int $id)
    {
        $collectionKeyName = '';
        if (true === $backToImport) {
            $data = [];
        } else {
            $isEntityEndsWithS = 's' === substr($entityName, -1);
            $collectionKeyName = sprintf('%s%s', strtolower($entityName), $isEntityEndsWithS ? 'es' : 's');
            $dateTime = new DateTime();
            $data = [
                'created_at' => $saveDateTime = sprintf(
                    'Data utworzenia exportu: %s',
                    $dateTime->format('H:i:s d.m.Y')
                ),
                $collectionKeyName => [],
            ];
        }

        $entityArray = $this->chooseEntityToExport($entityName, $id);
        if (is_array($entityArray)) {
            foreach ($entityArray as $entity) {
                if (true === $backToImport) {
                    $data[] = $entity;
                } else {
                    $data[$collectionKeyName][] = $entity;
                }
            }
        }

        if (is_object($entityArray)) {
            if (true === $backToImport) {
                $data = $entityArray;
            } else {
                $data[$collectionKeyName][] = $entityArray;
            }
        }

        return $data;
    }

    /**
     * @param string $entity
     * @param int|null $id
     *
     * @return array|object
     */
    private function chooseEntityToExport(string $entity, ?int $id)
    {
        if (null === $id) {
            return $this->entityManager->getRepository(sprintf('AppBundle\Entity\%s', ucfirst($entity)))->findAll();
        }

        return $this->entityManager->getRepository(sprintf('AppBundle\Entity\%s', ucfirst($entity)))->find($id);
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
