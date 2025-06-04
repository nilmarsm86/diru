<?php

namespace App\Entity;

use App\Entity\Traits\NameToStringTrait;
use App\Repository\BuildingRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: BuildingRepository::class)]
class Building
{
    use NameToStringTrait;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'buildings')]
    #[ORM\JoinColumn(nullable: true)]
    #[Assert\Valid]
//    #[Assert\NotBlank(message: 'Seleccione o cree la constructora que desarrolla la obra.')]
    private ?Constructor $constructor = null;

    #[ORM\Column(type: Types::BIGINT)]
    #[Assert\PositiveOrZero(message: 'El valor debe ser positivo')]
    private ?int $estimatedValueConstruction = null;

    #[ORM\Column(type: Types::BIGINT)]
    #[Assert\PositiveOrZero(message: 'El valor debe ser positivo')]
    private ?int $estimatedValueEquipment = null;

    #[ORM\Column(type: Types::BIGINT)]
    #[Assert\PositiveOrZero(message: 'El valor debe ser positivo')]
    private ?int $estimatedValueOther = null;

    #[ORM\Column(type: Types::BIGINT)]
    #[Assert\PositiveOrZero(message: 'El valor debe ser positivo')]
    private ?int $approvedValueConstruction = null;

    #[ORM\Column(type: Types::BIGINT)]
    #[Assert\PositiveOrZero(message: 'El valor debe ser positivo')]
    private ?int $approvedValueEquipment = null;

    #[ORM\Column(type: Types::BIGINT)]
    #[Assert\PositiveOrZero(message: 'El valor debe ser positivo')]
    private ?int $approvedValueOther = null;

    #[ORM\ManyToOne(inversedBy: 'buildings')]
    #[ORM\JoinColumn(nullable: true)]
    #[Assert\Valid]
    #[Assert\NotBlank(message: 'Seleccione o cree el proyecto a la cual pertenece la obra.')]
    private ?Project $project = null;

    public function __construct()
    {
        $this->estimatedValueConstruction = 0;
        $this->estimatedValueEquipment = 0;
        $this->estimatedValueOther = 0;

        $this->approvedValueConstruction = 0;
        $this->approvedValueEquipment = 0;
        $this->approvedValueOther = 0;
    }

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

    public function getEstimatedValueConstruction(): ?int
    {
        return $this->estimatedValueConstruction;
    }

    public function setEstimatedValueConstruction(?int $estimatedValueConstruction): static
    {
        $this->estimatedValueConstruction = $estimatedValueConstruction;

        return $this;
    }

    public function getEstimatedValueEquipment(): ?string
    {
        return $this->estimatedValueEquipment;
    }

    public function setEstimatedValueEquipment(?int $estimatedValueEquipment): static
    {
        $this->estimatedValueEquipment = $estimatedValueEquipment;

        return $this;
    }

    public function getEstimatedValueOther(): ?int
    {
        return $this->estimatedValueOther;
    }

    public function setEstimatedValueOther(?int $estimatedValueOther): static
    {
        $this->estimatedValueOther = $estimatedValueOther;

        return $this;
    }

    public function getApprovedValueConstruction(): ?int
    {
        return $this->approvedValueConstruction;
    }

    public function setApprovedValueConstruction(?int $approvedValueConstruction): static
    {
        $this->approvedValueConstruction = $approvedValueConstruction;

        return $this;
    }

    public function getApprovedValueEquipment(): ?int
    {
        return $this->approvedValueEquipment;
    }

    public function setApprovedValueEquipment(?int $approvedValueEquipment): static
    {
        $this->approvedValueEquipment = $approvedValueEquipment;

        return $this;
    }

    public function getApprovedValueOther(): ?int
    {
        return $this->approvedValueOther;
    }

    public function setApprovedValueOther(?int $approvedValueOther): static
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

    public function getProject(): ?Project
    {
        return $this->project;
    }

    public function setProject(?Project $project): static
    {
        $this->project = $project;

        return $this;
    }


}
