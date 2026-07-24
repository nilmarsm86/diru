<?php

namespace App\Entity;

use App\Entity\Traits\StartedAndFinishedTrait;
use App\Repository\PlannerProjectRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PlannerProjectRepository::class)]
class PlannerProject
{
    use StartedAndFinishedTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'plannersProjects')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\Valid]
    #[Assert\NotBlank(message: 'Establezca el planificador.')]
    private ?Planner $planner = null;

    #[ORM\ManyToOne(inversedBy: 'plannersProjects')]
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

    public function getPlanner(): ?Planner
    {
        return $this->planner;
    }

    public function setPlanner(?Planner $planner): static
    {
        $this->planner = $planner;

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

    public function hasPlanner(Planner $planner): bool
    {
        return $this->getPlanner()?->getId() === $planner->getId();
    }
}
