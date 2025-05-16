<?php

namespace App\Entity;

use App\Entity\Traits\NameToStringTrait;
use App\Repository\PersonRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
    #[Assert\NotBlank(message: 'Los apellidos están vacíos.')]
    #[Assert\NoSuspiciousCharacters]
    private ?string $lastname = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'El carnet de identidad está vacío.')]
    private ?string $identificationNumber = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $passport = null;

//    #[ORM\OneToMany(targetEntity: Client::class, mappedBy: 'person')]
//    private Collection $clients;
//
//    public function __construct()
//    {
//        $this->clients = new ArrayCollection();
//    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    /**
     * @param string|null $lastname
     * @return $this
     */
    public function setLastname(?string $lastname): static
    {
        $this->lastname = $lastname;

        return $this;
    }


    public function getIdentificationNumber(): ?string
    {
        return $this->identificationNumber;
    }

    public function setIdentificationNumber(?string $identificationNumber): static
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

//    /**
//     * @return Collection<int, Client>
//     */
//    public function getClients(): Collection
//    {
//        return $this->clients;
//    }
//
//    public function addClient(Client $client): static
//    {
//        if (!$this->clients->contains($client)) {
//            $this->clients->add($client);
//            $client->setPerson($this);
//        }
//
//        return $this;
//    }
//
//    public function removeClient(Client $client): static
//    {
//        if ($this->clients->removeElement($client)) {
//            // set the owning side to null (unless already changed)
//            if ($client->getPerson() === $this) {
//                $client->setPerson(null);
//            }
//        }
//
//        return $this;
//    }

    public function getFullName(): string
    {
        return $this->getName().' '.$this->getLastname();
    }
}
