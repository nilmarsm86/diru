<?php

namespace App\Entity;

use App\Repository\PlannerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PlannerRepository::class)]
class Planner extends Person
{
    /**
     * @var Collection<int, PlannerProject>
     */
    #[ORM\OneToMany(targetEntity: PlannerProject::class, mappedBy: 'planner', cascade: ['persist'])]
    #[Assert\Valid]
    private Collection $plannersProjects;

    public function __construct()
    {
        $this->plannersProjects = new ArrayCollection();
    }

    /**
     * @return Collection<int, PlannerProject>
     */
    public function getPlannersProjects(): Collection
    {
        return $this->plannersProjects;
    }

    public function getPlannerProjectByProject(Project $project): ?PlannerProject
    {
        foreach ($this->getPlannersProjects() as $plannersProject) {
            if ($plannersProject->getProject()?->getId() === $project->getId()) {
                return $plannersProject;
            }
        }

        return null;
    }

    public function addPlannerProject(PlannerProject $plannerProject): static
    {
        if (!$this->plannersProjects->contains($plannerProject)) {
            $this->plannersProjects->add($plannerProject);
        }

        return $this;
    }

    public function removePlannersProject(PlannerProject $plannerProject): static
    {
        $this->plannersProjects->removeElement($plannerProject);

        return $this;
    }

    /**
     * @return Collection<int, Project>
     */
    public function getProjects(): Collection
    {
        $projects = new ArrayCollection();
        foreach ($this->getPlannersProjects() as $plannersProject) {
            $projects->add($plannersProject->getProject());
        }

        return $projects;
    }

    public function addProject(Project $project): static
    {
        $plannerProject = new PlannerProject();
        $plannerProject->setProject($project);
        $plannerProject->setPlanner($this);

        $this->addPlannerProject($plannerProject);

        return $this;
    }

    public function removeProject(Project $project): static
    {
        $plannersProjects = $project->getPlannersProjects();
        foreach ($plannersProjects as $plannersProject) {
            if ($plannersProject->hasPlanner($this)) {
                $this->removePlannersProject($plannersProject);

                return $this;
            }
        }

        return $this;
    }
}
