<?php

namespace App\Entity;

use App\Entity\Traits\StartedAndFinishedTrait;
use App\Repository\ConstructorProjectRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ConstructorProjectRepository::class)]
class ConstructorProject
{
    use StartedAndFinishedTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(cascade: ['persist'], inversedBy: 'constructorProjects')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\Valid]
    #[Assert\NotBlank(message: 'Establezca la constructora.')]
    private ?Constructor $constructor = null;

    #[ORM\ManyToOne(inversedBy: 'constructorProjects')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\Valid]
    #[Assert\NotBlank(message: 'Establezca el proyecto.')]
    private ?Project $project = null;

    public function __construct()
    {
        $this->startedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getConstructor(): ?Constructor
    {
        return $this->constructor;
    }

    public function setConstructor(?Constructor $constructor): static
    {
        $this->constructor = $constructor;

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

    public function hasProject(Project $project): bool
    {
        return $this->getProject()?->getId() === $project->getId();
    }

    public function hasConstructor(Constructor $constructor): bool
    {
        return $this->getConstructor()?->getId() === $constructor->getId();
    }
}
