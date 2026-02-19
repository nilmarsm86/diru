<?php

namespace App\Entity;

use App\Repository\ProjectTechnicalPreparationEstimateRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProjectTechnicalPreparationEstimateRepository::class)]
class ProjectTechnicalPreparationEstimate extends Estimate
{
    #[ORM\ManyToOne(inversedBy: 'projectTechnicalPreparationEstimates')]
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
