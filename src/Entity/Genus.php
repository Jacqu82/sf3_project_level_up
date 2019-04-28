<?php

namespace App\Entity;

use App\Repository\GenusScientistRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\GenusRepository")
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
     * @ORM\OneToMany(
     *     targetEntity="App\Entity\GenusScientist",
     *      mappedBy="genus",
     *      fetch="EXTRA_LAZY",
     *     orphanRemoval=true,
     *     cascade={"persist"}
     * )
     * @Assert\Valid()
     */
    private $genusScientists;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\GenusNote", mappedBy="genus")
     * @ORM\OrderBy({"createdAt"="DESC"})
     */
    private $notes;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\SubFamily")
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

    public function setName($name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getSubFamily(): ?SubFamily
    {
        return $this->subFamily;
    }

    public function setSubFamily($subFamily): self
    {
        $this->subFamily = $subFamily;

        return $this;
    }

    public function getSpeciesCount(): ?int
    {
        return $this->speciesCount;
    }

    public function setSpeciesCount($speciesCount): self
    {
        $this->speciesCount = $speciesCount;

        return $this;
    }

    public function getFunFact(): ?string
    {
        return $this->funFact;
    }

    public function setFunFact($funFact): self
    {
        $this->funFact = $funFact;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return new \DateTime('-' . rand(0, 100) . ' days');
    }

    public function setIsPublished($isPublished): self
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
     * @return Collection|GenusScientist[]
     */
    public function getGenusScientists(): Collection
    {
        return $this->genusScientists;
    }

    public function getExpertScientist()
    {
        $criteria = GenusScientistRepository::createExpertCriteria();

        return $this->genusScientists->matching($criteria);

//        return $this->genusScientists->filter(function (GenusScientist $genusScientist) {
//            return $genusScientist->getYearsStudied() > 20;
//        });
    }

    public function addGenusScientist(GenusScientist $genusScientist): self
    {
        if (!$this->genusScientists->contains($genusScientist)) {
//            $this->genusScientist[] = $user;
            $this->genusScientists->add($genusScientist);

            // not needed for persistence, just keeping both sides in sync
            //$user->addStudiedGenus($this); //ManyToMany

            // needed to update the owning side of the relationship!
            $genusScientist->setGenus($this); //OneToMany
        }

        return $this;
    }

    public function removeGenusScientist(GenusScientist $genusScientist): self
    {
        if ($this->genusScientists->contains($genusScientist)) {
            $this->genusScientists->removeElement($genusScientist);

            // not needed for persistence, just keeping both sides in sync
            //$user->removeStudiedGenus($this); //ManyToMany

            // needed to update the owning side of the relationship!
            $genusScientist->setGenus(null); //OneToMany
        }

        return $this;
    }
}
