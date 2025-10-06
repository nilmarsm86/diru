<?php

namespace App\Entity;

use App\Entity\Traits\NameToStringTrait;
use App\Repository\SubsystemTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SubsystemTypeRepository::class)]
#[ORM\UniqueConstraint(name: 'subsystem_type_name', columns: ['name'])]
#[DoctrineAssert\UniqueEntity('name', message: 'El tipo de subsistema ya existe.')]
#[ORM\HasLifecycleCallbacks]
class SubsystemType
{
    use NameToStringTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToMany(targetEntity: SubsystemSubType::class, mappedBy: 'subsystemType', cascade: ['persist'], orphanRemoval: true)]
    #[Assert\Count(
        min: 1,
        minMessage: 'Debe establecer al menos 1 sub tipo para este tipo de subsistema.',
    )]
    #[Assert\Valid]
    #[ORM\OrderBy(["name" => "ASC"])]
    private Collection $subTypes;

    public function __construct()
    {
        $this->subTypes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, SubsystemSubType>
     */
    public function getSubTypes(): Collection
    {
        return $this->subTypes;
    }

    public function addSubType(SubsystemSubType $subType): static
    {
        if (!$this->subTypes->contains($subType)) {
            $this->subTypes->add($subType);
            $subType->setSubsystemType($this);
        }

        return $this;
    }

    public function removeSubType(SubsystemSubType $subType): static
    {
        if ($this->subTypes->removeElement($subType)) {
            // set the owning side to null (unless already changed)
            if ($subType->getSubsystemType() === $this) {
                $subType->setSubsystemType(null);
            }
        }

        return $this;
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function onSave(): void
    {
        $this->name = ucwords($this->getName());
    }

}
