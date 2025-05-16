<?php

namespace App\Entity;

use App\Entity\Traits\AddressTrait;
use App\Entity\Traits\PhoneAndEmailTrait;
use App\Repository\ClientRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ClientRepository::class)]
#[ORM\InheritanceType('JOINED')]
#[ORM\DiscriminatorColumn(name: 'discr', type: 'string')]
#[ORM\DiscriminatorMap([
    'individual' => 'IndividualClient',
    'enterprise' => 'EnterpriseClient',
])]
class Client
{
    use AddressTrait;
    use PhoneAndEmailTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    protected ?int $id = null;

    #[ORM\ManyToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: true)]
    #[Assert\Valid]
//    #[Assert\NotBlank(message: 'Llene los datos de la persona.')]
    protected ?Person $representative = null;

    #[ORM\Column(name: 'address', type: Types::TEXT)]
//    #[Assert\NotBlank(message: 'La dirección no debe estar vacia.')]
    private ?string $street = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRepresentative(): ?Person
    {
        return $this->representative;
    }

    public function setRepresentative(?Person $representative): static
    {
        $this->representative = $representative;

        return $this;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function setStreet(string $street): static
    {
        $this->street = $street;

        return $this;
    }

}
