<?php

namespace App\Entity;

use App\Repository\InvestmentRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InvestmentRepository::class)]
class Investment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $workName = null;

    #[ORM\Column(length: 255)]
    private ?string $investmentName = null;

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

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $betweenStreets = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $town = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $popularCouncil = null;

    #[ORM\ManyToOne(inversedBy: 'investments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?LocationZone $locationZone = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $block = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $district = null;

    #[ORM\Column(length: 255)]
    private ?string $street = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $addressNumber = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Municipality $municipality = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getWorkName(): ?string
    {
        return $this->workName;
    }

    public function setWorkName(string $workName): static
    {
        $this->workName = $workName;

        return $this;
    }

    public function getInvestmentName(): ?string
    {
        return $this->investmentName;
    }

    public function setInvestmentName(string $investmentName): static
    {
        $this->investmentName = $investmentName;

        return $this;
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

    public function getBetweenStreets(): ?string
    {
        return $this->betweenStreets;
    }

    public function setBetweenStreets(?string $betweenStreets): static
    {
        $this->betweenStreets = $betweenStreets;

        return $this;
    }

    public function getTown(): ?string
    {
        return $this->town;
    }

    public function setTown(?string $town): static
    {
        $this->town = $town;

        return $this;
    }

    public function getPopularCouncil(): ?string
    {
        return $this->popularCouncil;
    }

    public function setPopularCouncil(?string $popularCouncil): static
    {
        $this->popularCouncil = $popularCouncil;

        return $this;
    }

    public function getLocationZone(): ?LocationZone
    {
        return $this->locationZone;
    }

    public function setLocationZone(?LocationZone $locationZone): static
    {
        $this->locationZone = $locationZone;

        return $this;
    }

    public function getBlock(): ?string
    {
        return $this->block;
    }

    public function setBlock(?string $block): static
    {
        $this->block = $block;

        return $this;
    }

    public function getDistrict(): ?string
    {
        return $this->district;
    }

    public function setDistrict(?string $district): static
    {
        $this->district = $district;

        return $this;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function setStreet(string $street): static
    {
        $this->street = $street;

        return $this;
    }

    public function getAddressNumber(): ?string
    {
        return $this->addressNumber;
    }

    public function setAddressNumber(?string $addressNumber): static
    {
        $this->addressNumber = $addressNumber;

        return $this;
    }

    public function getMunicipality(): ?Municipality
    {
        return $this->municipality;
    }

    public function setMunicipality(?Municipality $municipality): static
    {
        $this->municipality = $municipality;

        return $this;
    }
}
