<?php

namespace AppBundle\Service\Export;

use Symfony\Component\Filesystem\Filesystem;

/**
 * @author Jacek Wesołowski <jacqu25@yahoo.com>
 */
class ExportProvider
{
    private $projectDir;

    public function __construct(string $projectDir)
    {
        $this->projectDir = $projectDir;
    }

    public function export(string $data, string $format, string $entityName, bool $backToImport, ?int $id): void
    {
        $this->findByDirectory($entityName);
        $entityNameExport = sprintf('%s%s', $entityName, $this->intentionalFilenameExport($backToImport, $id));
        $file = sprintf('%s/web/export/%s/%s.%s', $this->projectDir, $entityName, $entityNameExport, $format);
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

    private function intentionalFilenameExport(bool $backToImport, ?int $id): string
    {
        if (false === $backToImport && null === $id) {
            return 's';
        }

        if (false === $backToImport && null !== $id) {
            return '';
        }

        if (true === $backToImport && null === $id) {
            return 'Imports';
        }

        if (true === $backToImport && null !== $id) {
            return 'Import';
        }

        return '';
    }
}
