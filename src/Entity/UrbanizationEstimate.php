<?php

namespace App\Entity;

use App\Repository\UrbanizationEstimateRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UrbanizationEstimateRepository::class)]
class UrbanizationEstimate extends Estimate
{
}
