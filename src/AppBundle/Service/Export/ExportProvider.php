<?php

namespace AppBundle\Service\Export;

use Symfony\Component\Filesystem\Filesystem;

class ExportProvider
{
    private $projectDir;

    public function __construct(string $projectDir)
    {
        $this->projectDir = $projectDir;
    }

    public function export(string $data, string $format, string $entityName): void
    {
        $file = sprintf('%s/%s.%s', $this->projectDir, $entityName, $format);
        if (file_exists($file)) {
            $fileSystem = new Filesystem();
            $fileSystem->remove($file);
        }

        file_put_contents($file, $data);
    }
}
