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
use Symfony\Component\Serializer\Annotation\Ignore;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SubsystemTypeRepository::class)]
#[ORM\UniqueConstraint(name: 'subsystem_type_name', columns: ['name'])]
#[DoctrineAssert\UniqueEntity(fields: ['name'], message: 'Ya existeeste tipo de subsistema.', errorPath: 'name')]
#[ORM\HasLifecycleCallbacks]
class SubsystemType
{
    use NameToStringTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @var Collection<int, SubsystemSubType>
     */
    #[ORM\OneToMany(targetEntity: SubsystemSubType::class, mappedBy: 'subsystemType')]
    #[Assert\Count(
        min: 1,
        minMessage: 'Debe establecer al menos 1 subtipo para este tipo de subsistema.',
    )]
    #[Assert\Valid]
    #[ORM\OrderBy(["name" => "ASC"])]
    private Collection $subsystemSubTypes;

    #[ORM\Column(length: 255)]
    private ?string $classification = null;

    #[Assert\Choice(
        choices: SubsystemFunctionalClassification::CHOICES,
        message: 'Seleccione una clasificación.'
    )]
    private ?SubsystemFunctionalClassification $enumClassification = null;

    public function __construct()
    {
        $this->subsystemSubTypes = new ArrayCollection();
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
     * @return Collection<int, SubsystemSubType>
     */
    public function getSubsystemSubTypes(): Collection
    {
        return $this->subsystemSubTypes;
    }

    public function addSubsystemSubType(SubsystemSubType $subsystemSubType): static
    {
        if (!$this->subsystemSubTypes->contains($subsystemSubType)) {
            $this->subsystemSubTypes->add($subsystemSubType);
            $subsystemSubType->setSubsystemType($this);
        }

        return $this;
    }

    public function removeSubsystemSubType(SubsystemSubType $subsystemSubType): static
    {
        if ($this->subsystemSubTypes->removeElement($subsystemSubType)) {
            // set the owning side to null (unless already changed)
            if ($subsystemSubType->getSubsystemType() === $this) {
                $subsystemSubType->setSubsystemType(null);
            }
        }

        return $this;
    }

}
