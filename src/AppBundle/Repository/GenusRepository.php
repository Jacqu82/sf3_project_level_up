<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Genus;
use Doctrine\ORM\EntityRepository;

class GenusRepository extends EntityRepository
{
    /**
     * @return Genus[]
     */
    public function findAllPublishedOrderedBySize()
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.isPublished = :published')
            ->setParameter('published', true)
            ->orderBy('g.speciesCount', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
