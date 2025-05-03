<?php

namespace App\Entity;

use App\Entity\Traits\NameToStringTrait;
use App\Repository\OrganismRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;

#[ORM\Entity(repositoryClass: OrganismRepository::class)]
#[ORM\UniqueConstraint(name: 'organism_name', columns: ['name'])]
#[DoctrineAssert\UniqueEntity('name', message: 'El Organismo debe ser único.')]
class Organism
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
