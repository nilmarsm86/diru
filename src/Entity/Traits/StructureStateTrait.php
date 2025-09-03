<?php

namespace App\Entity\Traits;

use App\Entity\Enums\StructureState;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

trait StructureStateTrait
{
    #[ORM\Column(length: 255)]
    private ?string $state = null;

    #[Assert\Choice(choices: StructureState::CHOICES, message: 'Seleccione un estado válido.')]
    protected ?StructureState $enumState = null;

    /**
     * @return StructureState|null
     */
    public function getState(): ?StructureState
    {
        return $this->enumState;
    }

    /**
     * @param StructureState $state
     * @return $this
     */
    public function setState(StructureState $state): static
    {
        $this->enumState = $state;

        return $this;
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function onSaveState(): void
    {
        $this->state = $this->getState()?->value;
    }

    #[ORM\PostLoad]
    public function onLoadState(): void
    {
        $this->setState(StructureState::from($this->state));
    }

    /**
     * Is recent or not
     * @return bool
     */
    public function isRecent(): bool
    {
        return $this->enumState?->name === StructureState::Recent->name;
    }

    /**
     * Is existing without replicating or not
     * @return bool
     */
    public function isExistingWithoutReplicating(): bool
    {
        return $this->enumState?->name === StructureState::ExistingWithoutReplicating->name;
    }

    /**
     * Is existing replicated or not
     * @return bool
     */
    public function isExistingReplicated(): bool
    {
        return $this->enumState?->name === StructureState::ExistingReplicated->name;
    }

    /**
     * Is Replica or not
     * @return bool
     */
    public function isReplica(): bool
    {
        return $this->enumState?->name === StructureState::Replica->name;
    }

    /**
     * Recent
     * @return $this
     */
    public function recent(): static
    {
        $this->state = null;
        $this->setState(StructureState::Recent);
        return $this;
    }

    /**
     * ExistingWithoutReplicating
     * @return $this
     */
    public function existingWithoutReplicating(): static
    {
        $this->state = null;
        $this->setState(StructureState::ExistingWithoutReplicating);
        return $this;
    }

    /**
     * ExistingReplicated
     * @return $this
     */
    public function existingReplicated(): static
    {
        $this->state = null;
        $this->setState(StructureState::ExistingReplicated);
        return $this;
    }

    /**
     * Replica
     * @return $this
     */
    public function replica(): static
    {
        $this->state = null;
        $this->setState(StructureState::Replica);
        return $this;
    }
}