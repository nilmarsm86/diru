<?php

namespace App\Entity;

use App\Entity\Traits\AddressTrait;
use App\Repository\IndividualClientRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: IndividualClientRepository::class)]
class IndividualClient extends Client
{
    use AddressTrait;
}
