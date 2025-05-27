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
     * @var Collection<int, DraftsmanProyect>
     */
    #[ORM\OneToMany(targetEntity: DraftsmanProyect::class, mappedBy: 'draftsman', cascade: ['persist'])]
    private Collection $projects;

    public function __construct()
    {
        parent::__construct();
        $this->projects = new ArrayCollection();
    }

    /**
     * @return Collection<int, DraftsmanProyect>
     */
    public function getProjects(): Collection
    {
        return $this->projects;
    }

    public function addProject(DraftsmanProyect $project): static
    {
        if (!$this->projects->contains($project)) {
            $this->projects->add($project);
            $project->setDratfsman($this);
        }

        return $this;
    }

    public function removeProject(DraftsmanProyect $project): static
    {
        if ($this->projects->removeElement($project)) {
            // set the owning side to null (unless already changed)
            if ($project->getDratfsman() === $this) {
                $project->setDratfsman(null);
            }
        }

        return $this;
    }
}
