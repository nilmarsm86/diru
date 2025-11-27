<?php

namespace App\Entity;

use App\Repository\ProjectTechnicalPreparationEstimateRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProjectTechnicalPreparationEstimateRepository::class)]
class ProjectTechnicalPreparationEstimate extends Estimate
{

}
