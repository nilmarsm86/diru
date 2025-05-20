<?php

namespace App\Entity;

use App\Entity\Traits\NameToStringTrait;
use App\Repository\ConstructorRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;

#[ORM\Entity(repositoryClass: ConstructorRepository::class)]
#[ORM\UniqueConstraint(name: 'constructor_name', columns: ['name'])]
#[DoctrineAssert\UniqueEntity('name', message: 'El nombre de la constructora debe ser único.')]
#[DoctrineAssert\UniqueEntity('code', message: 'El código de la constructora debe ser único.')]

class Constructor
{
    use NameToStringTrait;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'El código está vacío.')]
    private ?string $code = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'El país está vacío.')]
    private ?string $country = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $logo = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): static
    {
        $this->code = $code;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): static
    {
        $this->country = $country;

        return $this;
    }

    public function getLogo(): ?string
    {
        return $this->logo;
    }

    public function setLogo(?string $logo): static
    {
        $this->logo = $logo;

        return $this;
    }
}
