<?php

namespace App\Entity;

use App\Entity\Traits\NameToStringTrait;
use App\Repository\IndividualClientRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: IndividualClientRepository::class)]
class IndividualClient extends Client
{
//    use AddressTrait;
    use NameToStringTrait;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Los apellidos estan vacío.')]
    #[Assert\NoSuspiciousCharacters]
    private ?string $lastname = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'El carnet de identidad está vacío.')]
    private ?string $identificationNumber = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $passport = null;
}
