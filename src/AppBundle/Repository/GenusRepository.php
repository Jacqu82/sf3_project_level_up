<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Genus;
use Doctrine\ORM\EntityRepository;

class GenusRepository extends EntityRepository
{
    /**
     * @return Genus[]
     */
    public function findAllPublishedOrderedByRecentlyActive()
    {
        return $this->createQueryBuilder('g')
            ->leftJoin('g.notes', 'n')
            ->leftJoin('g.genusScientists', 'genusScientists')
            ->addSelect('genusScientists')
            ->andWhere('g.isPublished = :published')
            ->setParameter('published', true)
            ->orderBy('n.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
