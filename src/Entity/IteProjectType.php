<?php

namespace App\Entity;

use App\Entity\Traits\NameToStringTrait;
use App\Repository\IteProjectTypeRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;

#[ORM\Entity(repositoryClass: IteProjectTypeRepository::class)]
#[ORM\UniqueConstraint(name: 'ite_project_type_name', columns: ['name'])]
#[DoctrineAssert\UniqueEntity('name', message: 'El tipo de ITE de proyecto ya existe.')]
class IteProjectType
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
