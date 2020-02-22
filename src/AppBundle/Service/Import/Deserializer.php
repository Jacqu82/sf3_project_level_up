<?php

namespace AppBundle\Service\Import;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\YamlEncoder;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Normalizer\JsonSerializableNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\PropertyNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * @author Jacek Wesołowski <jacqu25@yahoo.com>
 */
class Deserializer
{
    private $projectDir;

    private $entityManager;

    private $session;

    public function __construct(string $projectDir, EntityManagerInterface $entityManager, SessionInterface $session)
    {
        $this->projectDir = $projectDir;
        $this->entityManager = $entityManager;
        $this->session = $session;
    }

    public function prepareFileToImport(string $entityFile): void
    {
        list($fileName, $format) = explode('.', $entityFile);
        $id = null;
        if (strpos($fileName, '_') !== false) {
            list($fileName, $id) = explode('_', $fileName);
        }

        $entityFile = substr($fileName, 0, strpos($fileName, 'Import'));

        $this->deserialize($entityFile, $fileName, $format, (int)$id);
    }

    private function deserialize(string $entityName, string $fileName, string $format, int $id = null): void
    {
        $data = file_get_contents(
            sprintf('%s/web/import/%s/%s_%d.%s', $this->projectDir, $entityName, $fileName, $id, $format)
        );
        $entityClass = sprintf('AppBundle\Entity\%s', ucfirst($entityName));
        $entity = $this->entityManager->getRepository($entityClass)->find($id);

        $this->setDeserializer()->deserialize(
            $data,
            $entityClass,
            $format,
            [AbstractNormalizer::OBJECT_TO_POPULATE => $entity]
        );

        $this->entityManager->flush();

        $this->session->getFlashBag()->add(
            'success',
            sprintf('Import encji %s do bazy zakończony powodzeniem', $entityName)
        );
    }

    private function setDeserializer(): Serializer
    {
        $encoders = [new XmlEncoder(), new JsonEncoder(), new CsvEncoder(), new YamlEncoder()];
        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $normalizer = new ObjectNormalizer($classMetadataFactory, null, null, new ReflectionExtractor());

        return new Serializer(
            [
//                new EntityNormalizer($this->entityManager),
                $normalizer,
                new GetSetMethodNormalizer(),
                new ArrayDenormalizer(),
                new PropertyNormalizer(),
                new JsonSerializableNormalizer(),
//                new DateTimeNormalizer(),
//                new DataUriNormalizer(),
//                new DateIntervalNormalizer(),
            ], $encoders
        );
    }
}
