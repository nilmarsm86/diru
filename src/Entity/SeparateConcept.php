<?php

namespace App\Entity;

use App\Entity\Enums\SeparateConceptType;
use App\Entity\Traits\NameToStringTrait;
use App\Repository\SeparateConceptRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;


#[ORM\Entity(repositoryClass: SeparateConceptRepository::class)]
class SeparateConcept
{
    use NameToStringTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    #[Assert\Choice(
        choices: SeparateConceptType::CHOICES,
        message: 'Seleccione un tipo de concepto.'
    )]
    private SeparateConceptType $enumType;

    #[ORM\Column(length: 255)]
    private ?string $number = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $formula = null;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'children')]
    private ?self $parent = null;

    /**
     * @var Collection<int, self>
     */
    #[ORM\OneToMany(targetEntity: self::class, mappedBy: 'parent')]
    private Collection $children;

    /**
     * @var Collection<int, BuildingSeparateConcept>
     */
    #[ORM\OneToMany(targetEntity: BuildingSeparateConcept::class, mappedBy: 'separateConcept')]
    private Collection $buildingSeparateConcepts;

    public function __construct()
    {
        $this->children = new ArrayCollection();
        $this->buildingSeparateConcepts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): SeparateConceptType
    {
        return $this->enumType;
    }

    public function setType(SeparateConceptType $enumType): static
    {
        $this->type = '';
        $this->enumType = $enumType;
        return $this;
    }

    public function getNumber(): ?string
    {
        return $this->number;
    }

    public function setNumber(string $number): static
    {
        $this->number = $number;

        return $this;
    }

    public function getFormula(): ?string
    {
        return $this->formula;
    }

    public function setFormula(?string $formula): static
    {
        $this->formula = $formula;

        return $this;
    }

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): static
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getChildren(): Collection
    {
        return $this->children;
    }

    public function addChild(self $child): static
    {
        if (!$this->children->contains($child)) {
            $this->children->add($child);
            $child->setParent($this);
        }

        return $this;
    }

    public function removeChild(self $child): static
    {
        if ($this->children->removeElement($child)) {
            // set the owning side to null (unless already changed)
            if ($child->getParent() === $this) {
                $child->setParent(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, BuildingSeparateConcept>
     */
    public function getBuildingSeparateConcepts(): Collection
    {
        return $this->buildingSeparateConcepts;
    }

    public function addBuildingSeparateConcept(BuildingSeparateConcept $buildingSeparateConcept): static
    {
        if (!$this->buildingSeparateConcepts->contains($buildingSeparateConcept)) {
            $this->buildingSeparateConcepts->add($buildingSeparateConcept);
            $buildingSeparateConcept->setSeparateConcept($this);
        }

        return $this;
    }

    public function removeBuildingSeparateConcept(BuildingSeparateConcept $buildingSeparateConcept): static
    {
        if ($this->buildingSeparateConcepts->removeElement($buildingSeparateConcept)) {
            // set the owning side to null (unless already changed)
            if ($buildingSeparateConcept->getSeparateConcept() === $this) {
                $buildingSeparateConcept->setSeparateConcept(null);
            }
        }

        return $this;
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function onSave(): void
    {
        $this->type = $this->getType()->value;
    }

    #[ORM\PostLoad]
    public function onLoad(): void
    {
        $type = (is_null($this->type)) ? '' : $this->type;
        $this->setType(SeparateConceptType::from($type));
    }
}
