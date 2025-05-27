<?php

namespace App\Entity;

use App\Repository\RepresentativeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RepresentativeRepository::class)]
class Representative extends Person
{

}
