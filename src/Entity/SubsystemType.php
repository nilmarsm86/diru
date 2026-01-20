<?php

namespace App\Entity;

use App\Entity\Enums\SubsystemFunctionalClassification;
use App\Entity\Traits\NameToStringTrait;
use App\Repository\SubsystemTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SubsystemTypeRepository::class)]
#[ORM\HasLifecycleCallbacks]
class SubsystemType
{
    use NameToStringTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $classification = null;

    #[Assert\Choice(
        choices: SubsystemFunctionalClassification::CHOICES,
        message: 'Seleccione una clasificación.'
    )]
    private SubsystemFunctionalClassification $enumClassification;

    /**
     * @var Collection<int, SubsystemTypeSubsystemSubType>
     */
    #[ORM\OneToMany(targetEntity: SubsystemTypeSubsystemSubType::class, mappedBy: 'subsystemType', cascade: ['persist'], orphanRemoval: true)]
    #[Assert\Count(
        min: 1,
        minMessage: 'Debe establecer al menos 1 subtipo para este tipo.',
    )]
    #[Assert\Valid]
    private Collection $subsystemTypeSubsystemSubTypes;

    public function __construct()
    {
        $this->subsystemTypeSubsystemSubTypes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function onSave(): void
    {
        $this->classification = $this->getClassification()->value;
        $this->name = ucwords($this->getName());
    }

    /**
     * @throws \Exception
     */
    #[ORM\PostLoad]
    public function onLoad(): void
    {
        $classification = (is_null($this->classification)) ? '' : $this->classification;
        $this->setClassification(SubsystemFunctionalClassification::from($classification));
    }

    public function getClassification(): SubsystemFunctionalClassification
    {
        return $this->enumClassification;
    }

    public function setClassification(SubsystemFunctionalClassification $enumClassification): static
    {
        $this->classification = '';
        $this->enumClassification = $enumClassification;

        return $this;
    }

    /**
     * @return Collection<int, SubsystemTypeSubsystemSubType>
     */
    public function getSubsystemTypeSubsystemSubTypes(): Collection
    {
        return $this->subsystemTypeSubsystemSubTypes;
    }

    public function addSubsystemTypeSubsystemSubType(SubsystemTypeSubsystemSubType $subsystemTypeSubsystemSubType): static
    {
        if (!$this->subsystemTypeSubsystemSubTypes->contains($subsystemTypeSubsystemSubType)) {
            $this->subsystemTypeSubsystemSubTypes->add($subsystemTypeSubsystemSubType);
            $subsystemTypeSubsystemSubType->setSubsystemType($this);
        }

        return $this;
    }

    public function removeSubsystemTypeSubsystemSubType(SubsystemTypeSubsystemSubType $subsystemTypeSubsystemSubType): static
    {
        if ($this->subsystemTypeSubsystemSubTypes->removeElement($subsystemTypeSubsystemSubType)) {
            // set the owning side to null (unless already changed)
            if ($subsystemTypeSubsystemSubType->getSubsystemType() === $this) {
                $subsystemTypeSubsystemSubType->setSubsystemType(null);
            }
        }

        return $this;
    }
}
