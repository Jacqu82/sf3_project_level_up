<?php

namespace AppBundle\Service\Export;

use Symfony\Component\Filesystem\Filesystem;

class ExportProvider
{
    public function export(string $data, string $format = 'json'): string
    {
        $file = sprintf('%s/data.%s', $this->projectDir, $format);
        if (file_exists($file)) {
            $fileSystem = new Filesystem();
            $fileSystem->remove($file);
        }

        file_put_contents($file, $data);
    }
}
