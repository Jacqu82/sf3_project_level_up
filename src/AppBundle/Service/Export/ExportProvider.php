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
        $this->findByDirectory($entityName);
        $file = sprintf('%s/web/export/%s/%s.%s', $this->projectDir, $entityName, $entityName, $format);
        if (file_exists($file)) {
            $fileSystem = new Filesystem();
            $fileSystem->remove($file);
        }

        file_put_contents($file, $data);
    }

    private function findByDirectory(string $entityName): void
    {
        $directory = sprintf('%s/web/export/%s', $this->projectDir, $entityName);
        if (false === is_dir($directory)) {
            mkdir($directory, 0777, true);
        }
    }
}
