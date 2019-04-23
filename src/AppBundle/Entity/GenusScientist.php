<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\GenusScientistRepository")
 * @ORM\Table(name="genus_scientist")
 * @UniqueEntity(fields={"genus", "user"}, message="This user is already studying this genus", errorPath="user")
 */
class GenusScientist
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Genus", inversedBy="genusScientists")
     * @ORM\JoinColumn(nullable=false)
     */
    private $genus;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="studiedGenuses")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank()
     */
    private $yearsStudied;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getGenus()
    {
        return $this->genus;
    }

    /**
     * @param mixed $genus
     * @return GenusScientist
     */
    public function setGenus($genus)
    {
        $this->genus = $genus;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     * @return GenusScientist
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getYearsStudied()
    {
        return $this->yearsStudied;
    }

    /**
     * @param mixed $yearsStudied
     * @return GenusScientist
     */
    public function setYearsStudied($yearsStudied)
    {
        $this->yearsStudied = $yearsStudied;

        return $this;
    }
}
