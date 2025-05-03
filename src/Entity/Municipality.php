<?php

namespace App\Entity;

use App\Entity\Traits\NameToStringTrait;
use App\Repository\MunicipalityRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use Symfony\Component\Serializer\Annotation\Ignore;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: MunicipalityRepository::class)]
//#[ORM\UniqueConstraint(name: 'municipality_name', columns: ['name'])]
//#[DoctrineAssert\UniqueEntity('name', message: 'El municipio debe ser único.')]
class Municipality
{
    use NameToStringTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'municipalities')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\Valid]
    #[Ignore]
    private ?Province $province=null;

    public function __construct()
    {
        //$this->name = $name;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProvince(): ?Province
    {
        return $this->province;
    }

    public function setProvince(Province $province): static
    {
        $this->province = $province;

        return $this;
    }

    /*public function __serialize(): array
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
        ];
    }*/


}
