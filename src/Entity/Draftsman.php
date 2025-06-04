<?php

namespace App\Entity;

use App\Repository\DraftsmanRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DraftsmanRepository::class)]
class Draftsman extends Person
{
    /**
     * @var Collection<int, DraftsmanProject>
     */
    #[ORM\OneToMany(targetEntity: DraftsmanProject::class, mappedBy: 'draftsman', cascade: ['persist'])]
    private Collection $draftsmansProjects;

    public function __construct()
    {
        parent::__construct();
        $this->draftsmansProjects = new ArrayCollection();
    }

    /**
     * @return Collection<int, DraftsmanProject>
     */
    public function getDraftsmansProjects(): Collection
    {
        return $this->draftsmansProjects;
    }

    public function addDraftsmanProject(DraftsmanProject $draftsmanProject): static
    {
        if (!$this->draftsmansProjects->contains($draftsmanProject)) {
            $this->draftsmansProjects->add($draftsmanProject);
        }

        return $this;
    }

    public function removeDraftsmansProjects(DraftsmanProject $draftsmanProject): static
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
        foreach ($this->getDraftsmansProjects() as $draftsmansProjects){
            $projects->add($draftsmansProjects->getProject());
        }
        return $projects;
    }

    public function addProject(Project $project): static
    {
        $draftsmanProject = new DraftsmanProject();
        $draftsmanProject->setProject($project);
        $draftsmanProject->setDraftsman($this);
        $draftsmanProject->setStartedAt(new \DateTimeImmutable());

        $this->addDraftsmanProject($draftsmanProject);

        return $this;
    }

    public function removeProject(Project $project): static
    {
        $draftsmansProjects = $project->getDraftsmansProjects();
        foreach ($draftsmansProjects as $draftsmanProject){
            if($draftsmanProject->hasDraftsman($this)){
                $this->removeDraftsmansProjects($draftsmanProject);
                return $this;
            }
        }

        return $this;
    }
}
