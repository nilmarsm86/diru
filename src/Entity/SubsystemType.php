<?php

namespace App\Entity;

use App\Entity\Enums\SubsystemFunctionalClassification;
use App\Entity\Traits\NameToStringTrait;
use App\Repository\SubsystemTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SubsystemTypeRepository::class)]
//#[ORM\UniqueConstraint(name: 'subsystemtype_name', columns: ['name'])]
//#[DoctrineAssert\UniqueEntity('name', message: 'El tipo de subsistema ya existe.')]
#[ORM\HasLifecycleCallbacks]
class SubsystemType
{
    use NameToStringTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

//    #[ORM\ManyToMany(targetEntity: SubsystemSubType::class, mappedBy: 'subsystemTypes')]
//    #[Assert\Count(
//        min: 1,
//        minMessage: 'Debe establecer al menos 1 subtipo para este tipo.',
//    )]
//    #[Assert\Valid]
//    #[ORM\OrderBy(["name" => "ASC"])]
//    private Collection $subsystemSubTypes;

    #[ORM\Column(length: 255)]
    private ?string $classification = null;

    #[Assert\Choice(
        choices: SubsystemFunctionalClassification::CHOICES,
        message: 'Seleccione una clasificación.'
    )]
    private ?SubsystemFunctionalClassification $enumClassification = null;

    /**
     * @var Collection<int, SubsystemTypeSubsystemSubType>
     */
    #[ORM\OneToMany(targetEntity: SubsystemTypeSubsystemSubType::class, mappedBy: 'subsystemType', cascade: ['persist'], orphanRemoval: true)]
    #[Assert\Count(
        min: 1,
        minMessage: 'Debe establecer al menos 1 subtipo para este tipo.',
    )]
    #[Assert\Valid]
//    #[ORM\OrderBy(["name" => "ASC"])]
    private Collection $subsystemTypeSubsystemSubTypes;

    public function __construct()
    {
//        $this->subsystemSubTypes = new ArrayCollection();
        $this->subsystemTypeSubsystemSubTypes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

//    /**
//     * @return Collection<int, SubsystemSubType>
//     */
//    public function getSubsystemSubTypes(): Collection
//    {
//        return $this->subsystemSubTypes;
//    }
//
//    public function addSubsystemSubType(SubsystemSubType $subsystemSubType): static
//    {
//        if (!$this->subsystemSubTypes->contains($subsystemSubType)) {
//            $this->subsystemSubTypes->add($subsystemSubType);
//            $subsystemSubType->addSubsystemType($this);
//        }
//
//        return $this;
//    }
//
//    public function removeSubsystemSubType(SubsystemSubType $subsystemSubType): static
//    {
//        if ($this->subsystemSubTypes->removeElement($subsystemSubType)) {
//            // set the owning side to null (unless already changed)
////            if ($subsystemSubType->getSubsystemType() === $this) {
////                $subsystemSubType->setSubsystemType(null);
////            }
//            $subsystemSubType->removeSubsystemType($this);
//        }
//
//        return $this;
//    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function onSave(): void
    {
        $this->classification = $this->getClassification()->value;
        $this->name = ucwords($this->getName());
    }

    /**
     * @throws Exception
     */
    #[ORM\PostLoad]
    public function onLoad(): void
    {
        $this->setClassification(SubsystemFunctionalClassification::from($this->classification));
    }

    public function getClassification(): ?SubsystemFunctionalClassification
    {
        return $this->enumClassification;
    }

    public function setClassification(?SubsystemFunctionalClassification $enumClassification): static
    {
        $this->classification = "";
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
