<?php

namespace App\Entity;

use App\Repository\JustValueEstimateRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: JustValueEstimateRepository::class)]
class JustValueEstimate extends Estimate
{
    #[ORM\ManyToOne(inversedBy: 'justValueEstimates')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Building $building = null;

    public function getBuilding(): ?Building
    {
        return $this->building;
    }

    public function setBuilding(?Building $building): static
    {
        $this->building = $building;

        return $this;
    }

    public function getFormatedTotalPrice(): string
    {
        return number_format($this->getTotalPrice() / 100, 2).' '.$this->getBuilding()?->getProjectCurrency();
    }

    public function getFormatedPrice(): string
    {
        return number_format((float) $this->getPrice() / 100, 2).' '.$this->getBuilding()?->getProjectCurrency();
    }
}
