<?php

namespace AppBundle\Service\Import;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\YamlEncoder;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
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

    public function deserialize()
    {
        $data = file_get_contents(sprintf('%s/web/import/user.json', $this->projectDir));

//        dump($data);die;

        $arrayObject = $this->setDeserializer()->deserialize($data, 'AppBundle\Entity\User[]', 'json');

        dump($arrayObject);
        die;
    }

    private function setDeserializer(): Serializer
    {
        $encoders = [new XmlEncoder(), new JsonEncoder(), new CsvEncoder(), new YamlEncoder()];
        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $normalizer = new ObjectNormalizer($classMetadataFactory);

        return new Serializer(
            [
                $normalizer,
                new ArrayDenormalizer(),
//                new GetSetMethodNormalizer(),
//                new PropertyNormalizer(),
//                new JsonSerializableNormalizer(),
                new EntityNormalizer($this->entityManager)
//                new DateTimeNormalizer(),
//                new DataUriNormalizer(),
//                new DateIntervalNormalizer(),
            ], $encoders
        );
    }
}
