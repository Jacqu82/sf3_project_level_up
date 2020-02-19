<?php

namespace AppBundle\Service\Import;

use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @author Jacek Wesołowski <jacqu25@yahoo.com>
 */
class ImportProvider
{
    private $projectDir;

    public function __construct(string $projectDir)
    {
        $this->projectDir = $projectDir;
    }

    public function moveUploadedFile(UploadedFile $uploadedFile, array $entities): string
    {
        $directory = '';
        $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
        foreach ($entities as $entity) {
            if (strpos($originalFilename, $entity) !== false) {
                $directory = $this->findByDirectory($entity);
                break;
            }
        }

        $originalFilename = sprintf('%s.%s', $originalFilename, $uploadedFile->getClientOriginalExtension());
        $uploadedFile->move($directory, $originalFilename);

        return $originalFilename;
    }

    private function findByDirectory(string $entityName): string
    {
        $directory = sprintf('%s/web/import/%s', $this->projectDir, $entityName);
        if (false === is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        return $directory;
    }
}
