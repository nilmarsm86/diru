<?php

namespace App\Entity;

use App\Entity\Traits\NameToStringTrait;
use App\Repository\SubsystemSubTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SubsystemSubTypeRepository::class)]
#[ORM\HasLifecycleCallbacks]
class SubsystemSubType
{
    use NameToStringTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @var Collection<int, SubsystemTypeSubsystemSubType>
     */
    #[ORM\OneToMany(targetEntity: SubsystemTypeSubsystemSubType::class, mappedBy: 'subsystemSubType', cascade: ['persist'])]
    private Collection $subsystemTypeSubsystemSubTypes;

    public function __construct()
    {
        $this->subsystemTypeSubsystemSubTypes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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
            $subsystemTypeSubsystemSubType->setSubsystemSubType($this);
        }

        return $this;
    }

    public function removeSubsystemTypeSubsystemSubType(SubsystemTypeSubsystemSubType $subsystemTypeSubsystemSubType): static
    {
        if ($this->subsystemTypeSubsystemSubTypes->removeElement($subsystemTypeSubsystemSubType)) {
            // set the owning side to null (unless already changed)
            if ($subsystemTypeSubsystemSubType->getSubsystemSubType() === $this) {
                $subsystemTypeSubsystemSubType->setSubsystemSubType(null);
            }
        }

        return $this;
    }
}
