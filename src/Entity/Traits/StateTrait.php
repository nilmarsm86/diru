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
    protected StateEnum $enumState;

    public function getState(): StateEnum
    {
        return $this->enumState;
    }

    /**
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
        $state = (is_null($this->state)) ? '' : $this->state;
        $this->setState(StateEnum::from($state));
    }

    /**
     * Is active or not.
     */
    public function isActive(): bool
    {
        return $this->enumState->name === StateEnum::Active->name;
    }

    /**
     * Activate user.
     *
     * @return $this
     */
    public function activate(): static
    {
        $this->state = null;
        $this->setState(StateEnum::Active);

        return $this;
    }

    /**
     * Deactivate user.
     *
     * @return $this
     */
    public function deactivate(): static
    {
        $this->state = null;
        $this->setState(StateEnum::Inactive);

        return $this;
    }
}
