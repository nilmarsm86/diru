<?php

namespace App\Entity;

use App\Repository\ContractRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;

#[ORM\Entity(repositoryClass: ContractRepository::class)]
#[ORM\UniqueConstraint(name: 'contract_code', columns: ['code'])]
#[DoctrineAssert\UniqueEntity(fields: ['code'], message: 'Ya existe un contrato con este código.')]
class Contract
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'El código está vacío.')]
    private ?string $code = null;

    #[ORM\Column()]
    #[Assert\NotBlank(message: 'El año está vacío.')]
    private ?int $year = null;

    #[ORM\OneToOne(mappedBy: 'contract', cascade: ['persist'])]
    #[Assert\Valid]
    #[Assert\NotBlank(message: 'Establezca el proyecto')]
    private ?Project $project = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): static
    {
        $this->code = $code;

        return $this;
    }

    public function getYear(): ?int
    {
        return $this->year;
    }

    public function setYear(int $year): static
    {
        $this->year = $year;

        return $this;
    }

    public function getProject(): ?Project
    {
        return $this->project;
    }

    public function setProject(?Project $project): static
    {
        $this->project = $project;

        return $this;
    }
}
