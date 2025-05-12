<?php

namespace App\Entity;

use App\Entity\Traits\NameToStringTrait;
use App\Repository\PersonRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PersonRepository::class)]
#[ORM\UniqueConstraint(name: 'person_identification_number', columns: ['identification_number'])]
#[ORM\UniqueConstraint(name: 'person_passport', columns: ['passport'])]
#[DoctrineAssert\UniqueEntity(fields: ['passport'], message: 'Ya existe una persona con este número de pasaporte.')]
#[DoctrineAssert\UniqueEntity(fields: ['identificationNumber'], message: 'Ya existe una persona con este número de identificación.')]
class Person
{
    use NameToStringTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'El carnet de identidad está vacío.')]
    private ?string $identificationNumber = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $passport = null;

    #[ORM\OneToOne(targetEntity: Client::class, mappedBy: 'person')]
    private Client $client;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdentificationNumber(): ?string
    {
        return $this->identificationNumber;
    }

    public function setIdentificationNumber(string $identificationNumber): static
    {
        $this->identificationNumber = $identificationNumber;

        return $this;
    }

    public function getPassport(): ?string
    {
        return $this->passport;
    }

    public function setPassport(?string $passport): static
    {
        $this->passport = $passport;

        return $this;
    }

    public function __toString()
    {
        $data = $this->getName().' ('.$this->getIdentificationNumber().')';
        if($this->getPassport()){
             $data .= '['.$this->getPassport().']';
        }

        return $data;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): static
    {
        $this->client = $client;

        return $this;
    }
}
