<?php

namespace App\Entity;

use App\Entity\Traits\NameToStringTrait;
use App\Repository\PersonRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PersonRepository::class)]
#[ORM\InheritanceType('JOINED')]
#[ORM\DiscriminatorColumn(name: 'discr', type: 'string', options: ['default' => 'other'])]
#[ORM\DiscriminatorMap([
    'representative' => 'Representative',
    'draftsman' => 'Draftsman',
    'user' => 'Person'
])]
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
    protected ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Los apellidos están vacíos.')]
    #[Assert\NoSuspiciousCharacters]
    protected ?string $lastname = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'El carnet de identidad está vacío.')]
    protected ?string $identificationNumber = null;

    #[ORM\Column(length: 255, nullable: true)]
    protected ?string $passport = null;

//    /**
//     * @var Collection<int, Project>
//     */
//    #[ORM\ManyToMany(targetEntity: Project::class, mappedBy: 'draftsmans')]
//    private Collection $projects;

    public function __construct()
    {
//        $this->projects = new ArrayCollection();
    }

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
//        $data = $this->getName() . ' (' . $this->getIdentificationNumber() . ')';
//        if ($this->getPassport()) {
//            $data .= '[' . $this->getPassport() . ']';
//        }
//
//        return $data;
        return $this->getFullName();
    }

    public function getFullName(): string
    {
        return $this->getName() . ' ' . $this->getLastname();
    }


}
