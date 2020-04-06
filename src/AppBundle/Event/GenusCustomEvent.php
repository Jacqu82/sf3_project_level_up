<?php

declare(strict_types=1);

namespace AppBundle\Event;

use AppBundle\Entity\Genus;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * @author Jacek Wesołowski <jacqu25@yahoo.com>
 */
class GenusCustomEvent extends Event
{
    public const NAME = 'genus.notes';

    private $genus;

    public function __construct(Genus $genus)
    {
        $this->genus = $genus;
    }

    public function setRyanFilenameByModulo(EntityManagerInterface $entityManager, int $moduloFileName): void
    {
        if (0 === $moduloFileName) {
            return;
        }

        $notes = $this->genus->getNotes()->toArray();
        foreach ($notes as $key => $note) {
            if ($key % $moduloFileName === 0) {
                $note->setUserAvatarFilename('ryan.jpeg');
            } else {
                $note->setUserAvatarFilename('leanna.jpeg');
            }
        }
        $entityManager->flush();
    }
}
