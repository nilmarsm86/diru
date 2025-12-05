<?php

namespace App\Entity;

use App\Repository\SubsystemTypeSubsystemSubTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SubsystemTypeSubsystemSubTypeRepository::class)]
class SubsystemTypeSubsystemSubType
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'subsystemTypeSubsystemSubTypes')]
    #[ORM\JoinColumn(nullable: true)]
    #[Assert\Valid]
    private ?SubsystemType $subsystemType = null;

    #[ORM\ManyToOne(inversedBy: 'subsystemTypeSubsystemSubTypes')]
    #[ORM\JoinColumn(nullable: true)]
    #[Assert\Valid]
    private ?SubsystemSubType $subsystemSubType = null;

    /**
     * @var Collection<int, SubSystem>
     */
    #[ORM\OneToMany(targetEntity: SubSystem::class, mappedBy: 'subsystemTypeSubsystemSubType')]
    private Collection $subSystems;

    public function __construct()
    {
        $this->subSystems = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSubsystemType(): ?SubsystemType
    {
        return $this->subsystemType;
    }

    public function setSubsystemType(?SubsystemType $subsystemType): static
    {
        $this->subsystemType = $subsystemType;

        return $this;
    }

    public function getSubsystemSubType(): ?SubsystemSubType
    {
        return $this->subsystemSubType;
    }

    public function setSubsystemSubType(?SubsystemSubType $subsystemSubType): static
    {
        $this->subsystemSubType = $subsystemSubType;

        return $this;
    }

    /**
     * @return Collection<int, SubSystem>
     */
    public function getSubSystems(): Collection
    {
        return $this->subSystems;
    }

    public function addSubSystem(SubSystem $subSystem): static
    {
        if (!$this->subSystems->contains($subSystem)) {
            $this->subSystems->add($subSystem);
            $subSystem->setSubsystemTypeSubsystemSubType($this);
        }

        return $this;
    }

    public function removeSubSystem(SubSystem $subSystem): static
    {
        if ($this->subSystems->removeElement($subSystem)) {
            // set the owning side to null (unless already changed)
            if ($subSystem->getSubsystemTypeSubsystemSubType() === $this) {
                $subSystem->setSubsystemTypeSubsystemSubType(null);
            }
        }

        return $this;
    }
}
