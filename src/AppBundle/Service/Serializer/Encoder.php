<?php

namespace AppBundle\Service\Serializer;

use Symfony\Component\Filesystem\Filesystem;

class Encoder
{
    private $projectDir;

    public function __construct(string $projectDir)
    {
        $this->projectDir = $projectDir;
    }

    public function encode(string $data, string $format = 'json'): bool
    {
        $file = sprintf('%s/data.%s', $this->projectDir, $format);
        if (file_exists($file)) {
            $fileSystem = new Filesystem();
            $fileSystem->remove($file);
        }

        file_put_contents($file, $data);

        return true;
    }
}
