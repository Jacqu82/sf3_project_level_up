<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 * @ORM\Table(name="user")
 * @UniqueEntity(fields={"email"}, message="It looks like you already have an account!")
 * @Serializer\ExclusionPolicy("all")
 *
 * @author Jacek Wesołowski <jacqu25@yahoo.com>
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     * @Groups({"export"})
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\GenusScientist", mappedBy="user")
     */
    private $studiedGenuses;

    /**
     * @Assert\NotBlank()
     * @Assert\Email()
     * @ORM\Column(type="string", unique=true)
     * @Serializer\Expose
     * @Groups({"export"})
     */
    private $email;

    /**
     * The encoded password
     *
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * A non-persisted field that's used to create the encoded password.
     * @Assert\NotBlank(groups={"Registration"})
     *
     * @var string
     */
    private $plainPassword;

    /**
     * @ORM\Column(type="json_array")
     * @Groups({"export"})
     */
    private $roles = [];

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"export"})
     */
    private $isScientist = false;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Serializer\Expose
     * @Groups({"export"})
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Serializer\Expose
     * @Groups({"export"})
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Groups({"export"})
     */
    private $avatarUri;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Serializer\Expose
     * @Groups({"export"})
     */
    private $universityName;


    public function __construct()
    {
        $this->studiedGenuses = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    // needed by the security system
    public function getUsername()
    {
        return $this->email;
    }

    public function getRoles()
    {
        $roles = $this->roles;

        // give everyone ROLE_USER!
        if (!in_array('ROLE_USER', $roles)) {
            $roles[] = 'ROLE_USER';
        }

        return $roles;
    }

    public function setRoles(array $roles)
    {
        $this->roles = $roles;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function getSalt()
    {
        // leaving blank - I don't need/have a password!
    }

    public function eraseCredentials()
    {
        $this->plainPassword = null;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    public function setPlainPassword($plainPassword)
    {
        $this->plainPassword = $plainPassword;
        // forces the object to look "dirty" to Doctrine. Avoids
        // Doctrine *not* saving this entity, if only plainPassword changes
        $this->password = null;
    }

    public function isScientist()
    {
        return $this->isScientist;
    }

    public function setIsScientist($isScientist)
    {
        $this->isScientist = $isScientist;
    }

    public function getAvatarUri()
    {
        return $this->avatarUri;
    }

    public function setAvatarUri($avatarUri)
    {
        $this->avatarUri = $avatarUri;
    }

    public function getUniversityName()
    {
        return $this->universityName;
    }

    public function setUniversityName($universityName)
    {
        $this->universityName = $universityName;
    }

    public function getFullName()
    {
        return trim($this->getFirstName() . ' ' . $this->getLastName());
    }

    public function getFirstName()
    {
        return $this->firstName;
    }

    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    public function getLastName()
    {
        return $this->lastName;
    }

    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }

    /**
     * @return Collection|GenusScientist[]
     */
    public function getStudiedGenuses(): Collection
    {
        return $this->studiedGenuses;
    }

//    public function addStudiedGenus(GenusScientist $genus)
//    {
//        if ($this->studiedGenuses->contains($genus)) {
//            return;
//        }
//        $this->studiedGenuses[] = $genus;
//        //$genus->addGenusScientist($this);
//    }
//
//    public function removeStudiedGenus(GenusScientist $genus)
//    {
//        if (!$this->studiedGenuses->contains($genus)) {
//            return;
//        }
//        $this->studiedGenuses->removeElement($genus);
//        //$genus->removeGenusScientist($this);
//    }
}