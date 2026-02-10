<?php

namespace App\Entity;

use App\Repository\ProjectUrbanRegulationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProjectUrbanRegulationRepository::class)]
#[DoctrineAssert\UniqueEntity(fields: ['urbanRegulation', 'project'], message: 'Ya existe en el proyecto esta regulación.', errorPath: 'urbanRegulation')]
class ProjectUrbanRegulation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'projectUrbanRegulations')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\Valid]
    #[Assert\NotBlank(message: 'Establezca la regulación urbana.')]
    private ?UrbanRegulation $urbanRegulation = null;

    #[ORM\ManyToOne(inversedBy: 'projectUrbanRegulations')]
    #[ORM\JoinColumn(nullable: false)]
    //    #[Assert\Valid]
    #[Assert\NotBlank(message: 'Establezca el proyecto.')]
    private ?Project $project = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'Dato de la regulación aplicada.')]
    private ?string $data;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    protected ?string $reference = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUrbanRegulation(): ?UrbanRegulation
    {
        return $this->urbanRegulation;
    }

    public function setUrbanRegulation(?UrbanRegulation $urbanRegulation): static
    {
        $this->urbanRegulation = $urbanRegulation;

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

    public function getData(): ?string
    {
        return $this->data;
    }

    public function setData(?string $data): static
    {
        $this->data = $data;

        return $this;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(?string $reference): static
    {
        $this->reference = $reference;

        return $this;
    }
}
