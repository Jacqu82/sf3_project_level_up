<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\GenusRepository")
 * @ORM\Table(name="genus")
 */
class Genus
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\User", inversedBy="studiedGenuses", fetch="EXTRA_LAZY")
     * @ORM\JoinTable(name="genus_scientist")
     */
    private $genusScientists;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\GenusNote", mappedBy="genus")
     * @ORM\OrderBy({"createdAt"="DESC"})
     */
    private $notes;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\SubFamily")
     * @Assert\NotBlank()
     */
    private $subFamily;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @ORM\Column(type="string", unique=true)
     * @Gedmo\Slug(fields={"name"})
     */
    private $slug;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank()
     * @Assert\Range(min="0", minMessage="Negative species! Come on...")
     */
    private $speciesCount;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $funFact;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isPublished = true;

    /**
     * @ORM\Column(type="date")
     * @Assert\NotBlank()
     */
    private $firstDiscoveredAt;

    public function __construct()
    {
        $this->notes = new ArrayCollection();
        $this->genusScientists = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getSubFamily(): ?SubFamily
    {
        return $this->subFamily;
    }

    public function setSubFamily(?SubFamily $subFamily): self
    {
        $this->subFamily = $subFamily;

        return $this;
    }

    public function getSpeciesCount(): ?int
    {
        return $this->speciesCount;
    }

    public function setSpeciesCount(?int $speciesCount): self
    {
        $this->speciesCount = $speciesCount;

        return $this;
    }

    public function getFunFact(): ?string
    {
        return $this->funFact;
    }

    public function setFunFact(?string $funFact): self
    {
        $this->funFact = $funFact;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return new \DateTime('-' . rand(0, 100) . ' days');
    }

    public function setIsPublished(?bool $isPublished): self
    {
        $this->isPublished = $isPublished;

        return $this;
    }

    /**
     * Get isPublished
     *
     * @return boolean
     */
    public function getIsPublished()
    {
        return $this->isPublished;
    }

    /**
     * Get notes
     *
     * @return ArrayCollection|GenusNote[]
     */
    public function getNotes()
    {
        return $this->notes;
    }

    public function getFirstDiscoveredAt()
    {
        return $this->firstDiscoveredAt;
    }
    public function setFirstDiscoveredAt(\DateTime $firstDiscoveredAt = null)
    {
        $this->firstDiscoveredAt = $firstDiscoveredAt;
    }

    public function getSlug()
    {
        return $this->slug;
    }

    public function setSlug($slug)
    {
        $this->slug = $slug;
    }

    /**
     * @return Collection|User[]
     */
    public function getGenusScientists(): Collection
    {
        return $this->genusScientists;
    }

    public function addGenusScientist(User $user): self
    {
        if (!$this->genusScientists->contains($user)) {
//            $this->genusScientist[] = $user;
            $this->genusScientists->add($user);
        }

        return $this;
    }

    public function removeGenusScientist(User $user): self
    {
        if ($this->genusScientists->contains($user)) {
            $this->genusScientists->removeElement($user);
        }

        return $this;
    }
}
