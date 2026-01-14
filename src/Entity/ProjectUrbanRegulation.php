<?php

namespace App\Entity;

use App\Repository\ConstructorBuildingRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ConstructorBuildingRepository::class)]
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
    #[Assert\Valid]
    #[Assert\NotBlank(message: 'Establezca el proyecto.')]
    private ?Project $project = null;

    #[ORM\Column]
    private ?string $data;

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
}
