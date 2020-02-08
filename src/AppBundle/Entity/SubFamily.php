<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SubFamilyRepository")
 * @ORM\Table(name="sub_family")
 */
class SubFamily
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $name;

    public function __toString()
    {
        return $this->name;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function isPropertyCollection(): array
    {
        $fields = [];
        foreach (get_object_vars($this) as $fieldName => $fieldValue) {
            if ($fieldValue instanceof PersistentCollection) {
                $fields[] = $fieldName;
            }
        }

        return $fields;
    }
}
