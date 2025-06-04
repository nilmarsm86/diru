<?php

namespace App\Entity;

use App\Entity\Traits\PhoneAndEmailTrait;
use App\Repository\RepresentativeRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;

#[ORM\Entity(repositoryClass: RepresentativeRepository::class)]
#[DoctrineAssert\UniqueEntity(fields: ['email'], message: 'Ya existe un representante con este correo.')]
class Representative extends Person
{
    use PhoneAndEmailTrait;
}
