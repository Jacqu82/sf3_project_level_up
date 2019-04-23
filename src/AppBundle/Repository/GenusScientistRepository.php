<?php

namespace AppBundle\Repository;

use AppBundle\Entity\GenusScientist;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityRepository;

class GenusScientistRepository extends EntityRepository
{
    /**
     * @return GenusScientist[]
     * @throws \Doctrine\ORM\Query\QueryException
     */
    public function findAllExperts()
    {
        return $this->createQueryBuilder('genusScientist')
            ->addCriteria(self::createExpertCriteria())
            ->getQuery()
            ->execute();
    }

    static public function createExpertCriteria()
    {
        return Criteria::create()
            ->andWhere(Criteria::expr()->gt('yearsStudied', 20))
            ->orderBy(['yearsStudied', 'DESC']);
    }
}
