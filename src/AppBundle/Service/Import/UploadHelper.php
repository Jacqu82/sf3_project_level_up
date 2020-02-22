<?php

namespace AppBundle\Service\Import;

use Exception;
use RuntimeException;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @author Jacek Wesołowski <jacqu25@yahoo.com>
 */
class UploadHelper
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
        $originalFilenameCut = substr($originalFilename, 0, strpos($originalFilename, 'Import'));
        foreach ($entities as $entity) {
            if ($originalFilenameCut === $entity) {
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

        $this->isDirectoryEmtpy($directory, $entityName);

        return $directory;
    }

    /**
     * @param string $directory
     * @param string $entityName
     *
     * @throws Exception
     */
    private function isDirectoryEmtpy(string $directory, string $entityName)
    {
        $finder = new Finder();
        $files = $finder->in($directory);
        if (true === $files->hasResults()) {
            throw new RuntimeException(sprintf('Dane dla encji %s są już przesłane!', $entityName));
        }
    }
}
