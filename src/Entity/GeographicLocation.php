<?php

namespace App\Entity;

use App\Entity\Traits\NameToStringTrait;
use App\Repository\GeographicLocationRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;

#[ORM\Entity(repositoryClass: GeographicLocationRepository::class)]
#[DoctrineAssert\UniqueEntity(fields: ['name'], message: 'Ya existe una Ubicación Goegráfica con este nombre.')]
class GeographicLocation
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
