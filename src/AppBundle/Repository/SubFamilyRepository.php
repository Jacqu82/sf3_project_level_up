<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class SubFamilyRepository extends EntityRepository
{
    public function findAllAlphabetical()
    {
        return $this->createQueryBuilder('s')
            ->orderBy('s.name', 'ASC');
    }
}
