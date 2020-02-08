<?php

namespace AppBundle\Service\Export;

use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\YamlEncoder;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer as SymfonySerializer;

class Serializer
{
    public function setSerializer(): SymfonySerializer
    {
        $encoders = [new XmlEncoder(), new JsonEncoder(), new CsvEncoder(), new YamlEncoder()];
        $normalizer = new ObjectNormalizer(null, new CamelCaseToSnakeCaseNameConverter());
        $normalizer->setIgnoredAttributes(array('studiedGenuses'));
        $normalizers = array($normalizer);

        return new SymfonySerializer($normalizers, $encoders);
    }

    public function serialize()
    {
        $data = $this->setSerializer()->serialize($data, $format);
    }

    public function getDataToSerialize()
    {

    }
}
