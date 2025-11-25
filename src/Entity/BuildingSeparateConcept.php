<?php

namespace App\Entity;

use App\Entity\Interfaces\MoneyInterface;
use App\Repository\BuildingSeparateConceptRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BuildingSeparateConceptRepository::class)]
class BuildingSeparateConcept implements MoneyInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'buildingSeparateConcepts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Building $building = null;

    #[ORM\ManyToOne(inversedBy: 'buildingSeparateConcepts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?SeparateConcept $separateConcept = null;

    #[ORM\Column]
    private ?float $percent = null;

    public function __construct()
    {
        $this->percent = 0;
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getSeparateConcept(): ?SeparateConcept
    {
        return $this->separateConcept;
    }

    public function setSeparateConcept(?SeparateConcept $separateConcept): static
    {
        $this->separateConcept = $separateConcept;

        return $this;
    }

    public function getPercent(): ?float
    {
        return $this->percent;
    }

    public function setPercent(float $percent): static
    {
        $this->percent = $percent;

        return $this;
    }

    public function getPrice(): int
    {
        return $this->getBuilding()->getPrice() * $this->getPercent() / 100;
    }

    public function getCurrency(): ?string
    {
        return $this->getBuilding()->getProjectCurrency();
    }
}
