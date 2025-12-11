<?php

namespace App\Entity;

use App\Entity\Traits\NameToStringTrait;
use App\Repository\ConstructiveSystemRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ConstructiveSystemRepository::class)]
#[ORM\HasLifecycleCallbacks]
class ConstructiveSystem
{
    use NameToStringTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @var Collection<int, LocalConstructiveAction>
     */
    #[ORM\OneToMany(targetEntity: LocalConstructiveAction::class, mappedBy: 'constructiveSystem')]
    private Collection $localsConstructiveAction;

    /**
     * @var Collection<int, LandNetworkConnectionConstructiveAction>
     */
    #[ORM\OneToMany(targetEntity: LandNetworkConnectionConstructiveAction::class, mappedBy: 'constructiveSystem')]
    private Collection $landNetworkConnectionsConstructiveAction;

    public function __construct()
    {
        $this->localsConstructiveAction = new ArrayCollection();
        $this->landNetworkConnectionsConstructiveAction = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, LocalConstructiveAction>
     */
    public function getLocalsConstructiveAction(): Collection
    {
        return $this->localsConstructiveAction;
    }

    public function addLocalConstructiveAction(LocalConstructiveAction $localConstructiveAction): static
    {
        if (!$this->localsConstructiveAction->contains($localConstructiveAction)) {
            $this->localsConstructiveAction->add($localConstructiveAction);
            $localConstructiveAction->setConstructiveSystem($this);
        }

        return $this;
    }

    public function removeLocalConstructiveAction(LocalConstructiveAction $localConstructiveAction): static
    {
        if ($this->localsConstructiveAction->removeElement($localConstructiveAction)) {
            // set the owning side to null (unless already changed)
            if ($localConstructiveAction->getConstructiveSystem() === $this) {
                $localConstructiveAction->setConstructiveSystem(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, LandNetworkConnectionConstructiveAction>
     */
    public function getLandNetworkConnectionConstructiveAction(): Collection
    {
        return $this->landNetworkConnectionsConstructiveAction;
    }

    public function addLandNetworkConnectionConstructiveAction(LandNetworkConnectionConstructiveAction $landNetworkConnectionConstructiveAction): static
    {
        if (!$this->landNetworkConnectionsConstructiveAction->contains($landNetworkConnectionConstructiveAction)) {
            $this->landNetworkConnectionsConstructiveAction->add($landNetworkConnectionConstructiveAction);
            $landNetworkConnectionConstructiveAction->setConstructiveSystem($this);
        }

        return $this;
    }

    public function removeLandNetworkConnectionConstructiveAction(LandNetworkConnectionConstructiveAction $landNetworkConnectionConstructiveAction): static
    {
        if ($this->landNetworkConnectionsConstructiveAction->removeElement($landNetworkConnectionConstructiveAction)) {
            // set the owning side to null (unless already changed)
            if ($landNetworkConnectionConstructiveAction->getConstructiveSystem() === $this) {
                $landNetworkConnectionConstructiveAction->setConstructiveSystem(null);
            }
        }

        return $this;
    }
}
