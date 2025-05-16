<?php

namespace App\Entity;

use App\Entity\Traits\NameToStringTrait;
use App\Repository\LocationZoneRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;

#[ORM\Entity(repositoryClass: LocationZoneRepository::class)]
#[DoctrineAssert\UniqueEntity(fields: ['name'], message: 'Ya existe una Zona de ubicación con este nombre.')]
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
