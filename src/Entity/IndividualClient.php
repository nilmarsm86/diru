<?php

namespace App\Entity;

use App\Entity\Traits\NameToStringTrait;
use App\Repository\IndividualClientRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: IndividualClientRepository::class)]
class IndividualClient extends Client
{
    #[ORM\ManyToOne(cascade: ['persist', 'remove'], fetch: 'EAGER')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\Valid]
    protected ?Person $person = null;

    public function getPerson(): ?Person
    {
        return $this->person;
    }

    public function setPerson(?Person $person): static
    {
        $this->person = $person;

        return $this;
    }
}
