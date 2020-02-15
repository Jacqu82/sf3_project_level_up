<?php

namespace AppBundle\Service\Import;

use AppBundle\Entity\User;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\YamlEncoder;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
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

    public function __construct(string $projectDir, EntityManagerInterface $entityManager)
    {
        $this->projectDir = $projectDir;
        $this->entityManager = $entityManager;
    }

    public function deserialize(): void
    {
        $data = file_get_contents(sprintf('%s/web/import/userImport.json', $this->projectDir));
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['id' => 5]);
        $this->setDeserializer()->deserialize($data, User::class, 'json', ['object_to_populate' => $user]);

        $this->entityManager->flush();
    }

    private function setDeserializer(): Serializer
    {
        $encoders = [new XmlEncoder(), new JsonEncoder(), new CsvEncoder(), new YamlEncoder()];
        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $normalizer = new ObjectNormalizer($classMetadataFactory);

        return new Serializer(
            [
//                new EntityNormalizer($this->entityManager),
                $normalizer,
                new ArrayDenormalizer(),
                new PropertyNormalizer(),
                new GetSetMethodNormalizer(),
                new JsonSerializableNormalizer()
//                new DateTimeNormalizer(),
//                new DataUriNormalizer(),
//                new DateIntervalNormalizer(),
            ], $encoders
        );
    }
}
