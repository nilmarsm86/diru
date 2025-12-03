<?php

namespace App\Entity;

use App\Repository\SubsystemTypeSubsystemSubTypeRepository;
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
}
