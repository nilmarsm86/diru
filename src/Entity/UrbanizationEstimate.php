<?php

namespace App\Entity;

use App\Repository\UrbanizationEstimateRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UrbanizationEstimateRepository::class)]
class UrbanizationEstimate
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $concept = null;

    #[ORM\Column(length: 255)]
    private ?string $measurementUnit = null;

    #[ORM\Column(type: Types::BIGINT)]
    private ?string $price = null;

    #[ORM\Column]
    private ?float $quantity = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $comment = null;

    #[ORM\ManyToOne(inversedBy: 'urbanizationEstimates')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Building $building = null;

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
        return $this->getPrice() * $this->getQuantity();
    }

    public function getFormatedTotalPrice(): string
    {
        return (number_format(((float)$this->getTotalPrice() / 100), 2)) . ' ' . $this->getBuilding()->getProjectCurrency();
    }

    public function getFormatedPrice(): string
    {
        return (number_format(((float)$this->getPrice() / 100), 2)) . ' ' . $this->getBuilding()->getProjectCurrency();
    }
}
