<?php

namespace App\Entity;

use App\Entity\Traits\NameToStringTrait;
use App\Repository\LocationZoneRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LocationZoneRepository::class)]
class LocationZone
{
    use NameToStringTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    public function getId(): ?int
    {
        return $this->id;
    }
}
