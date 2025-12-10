<?php

namespace App\Entity;

use App\Repository\EstimateRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EstimateRepository::class)]
#[ORM\InheritanceType('JOINED')]
#[ORM\DiscriminatorColumn(name: 'discr', type: 'string')]
#[ORM\DiscriminatorMap([
    'urbanization' => 'UrbanizationEstimate',
    'ptp' => 'ProjectTechnicalPreparationEstimate',
])]

class Estimate
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    protected ?int $id = null;

    #[ORM\Column(length: 255)]
    protected ?string $concept = null;

    #[ORM\Column(length: 255)]
    protected ?string $measurementUnit = null;

    #[ORM\Column(type: Types::BIGINT)]
    protected ?string $price = null;

    #[ORM\Column]
    protected ?float $quantity = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    protected ?string $comment = null;

    #[ORM\ManyToOne(inversedBy: 'urbanizationEstimates')]
    #[ORM\JoinColumn(nullable: false)]
    protected ?Building $building = null;

    public function __construct()
    {
        $this->price = 0;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getConcept(): ?string
    {
        return $this->concept;
    }

    public function setConcept(string $concept): static
    {
        $this->concept = $concept;

        return $this;
    }

    public function getMeasurementUnit(): ?string
    {
        return $this->measurementUnit;
    }

    public function setMeasurementUnit(string $measurementUnit): static
    {
        $this->measurementUnit = $measurementUnit;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getQuantity(): ?float
    {
        return $this->quantity;
    }

    public function setQuantity(float $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): static
    {
        $this->comment = $comment;

        return $this;
    }

    public function getBuilding(): ?Building
    {
        return $this->building;
    }

    public function setBuilding(?Building $building): static
    {
        $this->building = $building;

        return $this;
    }

    public function getTotalPrice(): float
    {
        return (float)$this->getPrice() * $this->getQuantity();
    }

    public function getFormatedTotalPrice(): string
    {
        return (number_format(((float)$this->getTotalPrice() / 100), 2)) . ' ' . $this->getBuilding()?->getProjectCurrency();
    }

    public function getFormatedPrice(): string
    {
        return (number_format(((float)$this->getPrice() / 100), 2)) . ' ' . $this->getBuilding()?->getProjectCurrency();
    }
}
