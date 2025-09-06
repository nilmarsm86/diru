<?php

namespace App\Entity;

use App\Entity\Enums\ConstructiveActionType;
use App\Entity\Traits\NameToStringTrait;
use App\Repository\ConstructiveActionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ConstructiveActionRepository::class)]
#[ORM\HasLifecycleCallbacks]
class ConstructiveAction
{
    use NameToStringTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    #[Assert\Choice(
        choices: [ConstructiveActionType::NoModifier, ConstructiveActionType::Modifier],
        message: 'Seleccione un tipo de acción constructiva.'
    )]
    private ConstructiveActionType $enumType;

    /**
     * @var Collection<int, Local>
     */
    #[ORM\OneToMany(targetEntity: LocalConstructiveAction::class, mappedBy: 'constructiveAction')]
    private Collection $localsConstructiveAction;

    /**
     * @var Collection<int, LandNetworkConnection>
     */
    #[ORM\OneToMany(targetEntity: LandNetworkConnectionConstructiveAction::class, mappedBy: 'constructiveAction')]
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

    public function getType(): ConstructiveActionType
    {
        return $this->enumType;
    }

    public function setType(ConstructiveActionType $enumType): static
    {
        $this->type = "";
        $this->enumType = $enumType;

        return $this;
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function onSave(): void
    {
        $this->type = $this->getType()->value;
    }

    /**
     * @throws Exception
     */
    #[ORM\PostLoad]
    public function onLoad(): void
    {
        $this->setType(ConstructiveActionType::from($this->type));
    }

    /**
     * @return Collection<int, Local>
     */
    public function getLocalsConstructiveAction(): Collection
    {
        return $this->localsConstructiveAction;
    }

    public function addLocalConstructiveAction(LocalConstructiveAction $localConstructiveAction): static
    {
        if (!$this->localsConstructiveAction->contains($localConstructiveAction)) {
            $this->localsConstructiveAction->add($localConstructiveAction);
            $localConstructiveAction->setConstructiveAction($this);
        }

        return $this;
    }

    public function removeLocalConstructiveAction(LocalConstructiveAction $localConstructiveAction): static
    {
        if ($this->localsConstructiveAction->removeElement($localConstructiveAction)) {
            // set the owning side to null (unless already changed)
            if ($localConstructiveAction->getConstructiveAction() === $this) {
                $localConstructiveAction->setConstructiveAction(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, LandNetworkConnection>
     */
    public function getLandNetworkConnectionConstructiveAction(): Collection
    {
        return $this->landNetworkConnectionsConstructiveAction;
    }

    public function addLandNetworkConnectionConstructiveAction(LandNetworkConnectionConstructiveAction $landNetworkConnectionConstructiveAction): static
    {
        if (!$this->landNetworkConnectionsConstructiveAction->contains($landNetworkConnectionConstructiveAction)) {
            $this->landNetworkConnectionsConstructiveAction->add($landNetworkConnectionConstructiveAction);
            $landNetworkConnectionConstructiveAction->setConstructiveAction($this);
        }

        return $this;
    }

    public function removeLandNetworkConnectionConstructiveAction(LandNetworkConnectionConstructiveAction $landNetworkConnectionConstructiveAction): static
    {
        if ($this->landNetworkConnectionsConstructiveAction->removeElement($landNetworkConnectionConstructiveAction)) {
            // set the owning side to null (unless already changed)
            if ($landNetworkConnectionConstructiveAction->getConstructiveAction() === $this) {
                $landNetworkConnectionConstructiveAction->setConstructiveAction(null);
            }
        }

        return $this;
    }
}
