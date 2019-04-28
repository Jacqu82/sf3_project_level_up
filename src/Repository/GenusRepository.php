<?php

namespace App\Repository;

use App\Entity\Genus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityRepository;

class GenusRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Genus::class);
    }

    /**
     * @return Genus[]
     */
    public function findAllPublishedOrderedByRecentlyActive()
    {
        return $this->createQueryBuilder('g')
            ->leftJoin('g.notes', 'n')
//            ->leftJoin('g.genusScientists', 'genusScientists')
//            ->addSelect('genusScientists')
            ->andWhere('g.isPublished = :published')
            ->setParameter('published', true)
            ->orderBy('n.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
