<?php

namespace App\Entity;

use App\Entity\Traits\StartedAndFinishedTrait;
use App\Repository\DraftsmanProjectRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DraftsmanProjectRepository::class)]
class DraftsmanProject
{
    use StartedAndFinishedTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'projects')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Draftsman $draftsman = null;

    #[ORM\ManyToOne(inversedBy: 'draftsmansProyects')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Project $project = null;

    public function __construct()
    {
        $this->startedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDraftsman(): ?Draftsman
    {
        return $this->draftsman;
    }

    public function setDraftsman(?Draftsman $draftsman): static
    {
        $this->draftsman = $draftsman;

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
        return $this->getProject()->getId() === $project->getId();
    }

    public function hasDraftsman(Draftsman $draftsman): bool
    {
        return $this->getDraftsman()->getId() === $draftsman->getId();
    }
}
