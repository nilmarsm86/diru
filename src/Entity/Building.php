<?php

namespace App\Entity;

use App\Entity\Traits\NameToStringTrait;
use App\Repository\BuildingRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;

#[ORM\Entity(repositoryClass: BuildingRepository::class)]
class Building
{
    use NameToStringTrait;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'investments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Constructor $constructor = null;

    #[ORM\Column(type: Types::BIGINT)]
    private ?string $estimatedValueConstruction = null;

    #[ORM\Column(type: Types::BIGINT)]
    private ?string $estimatedValueEquipment = null;

    #[ORM\Column(type: Types::BIGINT)]
    private ?string $estimatedValueOther = null;

    #[ORM\Column(type: Types::BIGINT, nullable: true)]
    private ?string $approvedValueConstruction = null;

    #[ORM\Column(type: Types::BIGINT, nullable: true)]
    private ?string $approvedValueEquipment = null;

    #[ORM\Column(type: Types::BIGINT, nullable: true)]
    private ?string $approvedValueOther = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getConstructor(): ?Constructor
    {
        return $this->constructor;
    }

    public function setConstructor(?Constructor $constructor): static
    {
        $this->constructor = $constructor;

        return $this;
    }

    public function getEstimatedValueConstruction(): ?string
    {
        return $this->estimatedValueConstruction;
    }

    public function setEstimatedValueConstruction(string $estimatedValueConstruction): static
    {
        $this->estimatedValueConstruction = $estimatedValueConstruction;

        return $this;
    }

    public function getEstimatedValueEquipment(): ?string
    {
        return $this->estimatedValueEquipment;
    }

    public function setEstimatedValueEquipment(string $estimatedValueEquipment): static
    {
        $this->estimatedValueEquipment = $estimatedValueEquipment;

        return $this;
    }

    public function getEstimatedValueOther(): ?string
    {
        return $this->estimatedValueOther;
    }

    public function setEstimatedValueOther(string $estimatedValueOther): static
    {
        $this->estimatedValueOther = $estimatedValueOther;

        return $this;
    }

    public function getApprovedValueConstruction(): ?string
    {
        return $this->approvedValueConstruction;
    }

    public function setApprovedValueConstruction(string $approvedValueConstruction): static
    {
        $this->approvedValueConstruction = $approvedValueConstruction;

        return $this;
    }

    public function getApprovedValueEquipment(): ?string
    {
        return $this->approvedValueEquipment;
    }

    public function setApprovedValueEquipment(?string $approvedValueEquipment): static
    {
        $this->approvedValueEquipment = $approvedValueEquipment;

        return $this;
    }

    public function getApprovedValueOther(): ?string
    {
        return $this->approvedValueOther;
    }

    public function setApprovedValueOther(?string $approvedValueOther): static
    {
        $this->approvedValueOther = $approvedValueOther;

        return $this;
    }

    public function getTotalEstimatedValue()
    {
        return $this->getEstimatedValueConstruction() + $this->getEstimatedValueEquipment() + $this->getEstimatedValueOther();
    }

    public function getTotalApprovedValue()
    {
        return $this->getApprovedValueConstruction() + $this->getApprovedValueEquipment() + $this->getApprovedValueOther();
    }

    //debo convertir el dinero en centavos, valores estimados y valores aprobados


}
