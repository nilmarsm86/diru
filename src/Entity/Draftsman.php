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
     * @var Collection<int, DraftsmanBuilding>
     */
    #[ORM\OneToMany(targetEntity: DraftsmanBuilding::class, mappedBy: 'draftsman', cascade: ['persist'])]
    private Collection $draftsmansBuildings;

    public function __construct()
    {
        $this->draftsmansBuildings = new ArrayCollection();
    }

    /**
     * @return Collection<int, DraftsmanBuilding>
     */
    public function getDraftsmansBuildings(): Collection
    {
        return $this->draftsmansBuildings;
    }

    /**
     * @param Building $building
     * @return DraftsmanBuilding|null
     */
    public function getDraftsmanBuildingByBuilding(Building $building): ?DraftsmanBuilding
    {
        foreach ($this->getDraftsmansBuildings() as $draftsmansBuilding){
            if($draftsmansBuilding->getBuilding()->getId() === $building->getId()){
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
        foreach ($this->getDraftsmansBuildings() as $draftsmansBuilding){
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
        foreach ($draftsmansBuildings as $draftsmansBuilding){
            if($draftsmansBuilding->hasDraftsman($this)){
                $this->removeDraftsmansBuilding($draftsmansBuilding);
                return $this;
            }
        }

        return $this;
    }
}
