<?php

namespace AppBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * @author Jacek Wesołowski <jacqu25@yahoo.com>
 */
class CheckIsImportExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('isImport', [$this, 'checkIsImport']),
        ];
    }

    public function checkIsImport(array $filenames, array $roles): array
    {
        $keys = [];
        foreach ($filenames as $key => $filename) {
            if (in_array('ROLE_ADMIN', $roles, true)) {
                return $filenames;
            }

            if (strpos($filename, 'Import') !== false) {
                $keys[] = $key;
            }
        }

        foreach ($keys as $key) {
            unset($filenames[$key]);
        }

        return $filenames;
    }
}
