<?php

namespace App\Entity;

use App\Repository\DraftsmanRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: DraftsmanRepository::class)]
class Draftsman extends Person
{
    /**
     * @var Collection<int, DraftsmanBuilding>
     */
    #[ORM\OneToMany(targetEntity: DraftsmanBuilding::class, mappedBy: 'draftsman', cascade: ['persist'])]
    #[Assert\Valid]
    private Collection $draftsmansBuildings;

    /**
     * @var Collection<int, DraftsmanProject>
     */
    #[ORM\OneToMany(targetEntity: DraftsmanProject::class, mappedBy: 'draftsman', cascade: ['persist'])]
    #[Assert\Valid]
    private Collection $draftsmansProjects;

    public function __construct()
    {
        $this->draftsmansBuildings = new ArrayCollection();
        $this->draftsmansProjects = new ArrayCollection();
    }

    /**
     * @return Collection<int, DraftsmanBuilding>
     */
    public function getDraftsmansBuildings(): Collection
    {
        return $this->draftsmansBuildings;
    }

    public function getDraftsmanBuildingByBuilding(Building $building): ?DraftsmanBuilding
    {
        foreach ($this->getDraftsmansBuildings() as $draftsmansBuilding) {
            if ($draftsmansBuilding->getBuilding()?->getId() === $building->getId()) {
                return $draftsmansBuilding;
            }
        }

        return null;
    }

    public function addDraftsmanBuilding(DraftsmanBuilding $draftsmanBuilding): static
    {
        if (!$this->draftsmansBuildings->contains($draftsmanBuilding)) {
            $this->draftsmansBuildings->add($draftsmanBuilding);
        }

        return $this;
    }

    public function removeDraftsmansBuilding(DraftsmanBuilding $draftsmanBuilding): static
    {
        $this->draftsmansBuildings->removeElement($draftsmanBuilding);

        return $this;
    }

    /**
     * @return Collection<int, Building>
     */
    public function getBuildings(): Collection
    {
        $buildings = new ArrayCollection();
        foreach ($this->getDraftsmansBuildings() as $draftsmansBuilding) {
            $buildings->add($draftsmansBuilding->getBuilding());
        }

        return $buildings;
    }

    public function addBuilding(Building $building): static
    {
        $draftsmanBuilding = new DraftsmanBuilding();
        $draftsmanBuilding->setBuilding($building);
        $draftsmanBuilding->setDraftsman($this);

        $this->addDraftsmanBuilding($draftsmanBuilding);

        return $this;
    }

    public function removeBuilding(Building $building): static
    {
        $draftsmansBuildings = $building->getDraftsmansBuildings();
        foreach ($draftsmansBuildings as $draftsmansBuilding) {
            if ($draftsmansBuilding->hasDraftsman($this)) {
                $this->removeDraftsmansBuilding($draftsmansBuilding);

                return $this;
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, DraftsmanProject>
     */
    public function getDraftsmansProjects(): Collection
    {
        return $this->draftsmansProjects;
    }

    public function getDraftsmanProjectByProject(Project $project): ?DraftsmanProject
    {
        foreach ($this->getDraftsmansProjects() as $draftsmansProject) {
            if ($draftsmansProject->getProject()?->getId() === $project->getId()) {
                return $draftsmansProject;
            }
        }

        return null;
    }

    public function addDraftsmanProject(DraftsmanProject $draftsmanProject): static
    {
        if (!$this->draftsmansProjects->contains($draftsmanProject)) {
            $this->draftsmansProjects->add($draftsmanProject);
        }

        return $this;
    }

    public function removeDraftsmansProject(DraftsmanProject $draftsmanProject): static
    {
        $this->draftsmansProjects->removeElement($draftsmanProject);

        return $this;
    }

    /**
     * @return Collection<int, Project>
     */
    public function getProjects(): Collection
    {
        $projects = new ArrayCollection();
        foreach ($this->getDraftsmansProjects() as $draftsmansProject) {
            $projects->add($draftsmansProject->getProject());
        }

        return $projects;
    }

    public function addProject(Project $project): static
    {
        $draftsmanProject = new DraftsmanProject();
        $draftsmanProject->setProject($project);
        $draftsmanProject->setDraftsman($this);

        $this->addDraftsmanProject($draftsmanProject);

        return $this;
    }

    public function removeProject(Project $project): static
    {
        $draftsmansProjects = $project->getDraftsmansProjects();
        foreach ($draftsmansProjects as $draftsmansProject) {
            if ($draftsmansProject->hasDraftsman($this)) {
                $this->removeDraftsmansProject($draftsmansProject);

                return $this;
            }
        }

        return $this;
    }
}
