<?php

namespace App\Entity\Traits;

use App\Entity\Enums\State as StateEnum;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

trait StateTrait
 {
    #[ORM\Column(length: 255)]
    protected ?string $state = null;

    #[Assert\Choice(choices: [StateEnum::Active, StateEnum::Inactive], message: 'Seleccione un estado válido.')]
    protected ?StateEnum $enumState = null;

    /**
     * @return StateEnum
     */
    public function getState(): StateEnum
    {
        return $this->enumState;
    }

    /**
     * @param StateEnum $state
     * @return $this
     */
    public function setState(StateEnum $state): static
    {
        $this->enumState = $state;

        return $this;
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function onSave(): void
    {
        $this->state = $this->getState()->value;
    }

    #[ORM\PostLoad]
    public function onLoad(): void
    {
        $this->setState(StateEnum::from($this->state));
    }

    /**
     * Is active or not
     * @return bool
     */
    public function isActive():bool
    {
        return $this->enumState?->name === StateEnum::Active->name;
    }

    /**
     * Activate user
     * @return $this
     */
    public function activate(): static
    {
        $this->state = null;
        $this->setState(StateEnum::Active);
        return $this;
    }

    /**
     * Deactivate user
     * @return $this
     */
    public function deactivate(): static
    {
        $this->state = null;
        $this->setState(StateEnum::Inactive);

        return $this;
    }
 }