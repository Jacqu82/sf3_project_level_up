<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository
{
    public function findAllScientists()
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.isScientist = :scientist')
            ->setParameter('scientist', true);
    }

}
