<?php

namespace App\Entity;

use App\Repository\LocalConstructiveActionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: LocalConstructiveActionRepository::class)]
class LocalConstructiveAction
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(mappedBy: 'localConstructiveAction', cascade: ['persist', 'remove'])]
    private ?Local $local = null;

    #[ORM\ManyToOne(inversedBy: 'localsConstructiveAction')]
    #[Assert\Valid]
    #[Assert\NotNull(message: 'Seleccione la acción constructiva.')]
    private ?ConstructiveAction $constructiveAction = null;

    #[ORM\Column(type: Types::BIGINT)]
    //    #[Assert\NotNull(message: 'Establezca el precio de la acción constructiva.')]
    #[Assert\NotBlank(message: 'Establezca el precio de la acción constructiva.')]
    #[Assert\PositiveOrZero(message: 'El valor debe ser positivo')]
    #[Assert\Expression(
        'this.validPrice()',
        message: 'El precio para esta acción constructiva debe ser mayor que 0.',
        negate: false
    )]
    private ?int $price = 0;

    #[ORM\ManyToOne(inversedBy: 'localsConstructiveAction')]
    #[Assert\Valid]
    #[Assert\NotNull(message: 'Seleccione el sistema constructivo.')]
    private ?ConstructiveSystem $constructiveSystem = null;

    public function validPrice(): bool
    {
        $values = ['', 'No es necesaria', 'Eliminación', 'Cambio de uso'];

        return false === in_array($this->constructiveAction?->getName(), $values, true) && 0 === $this->getPrice();
    }

    public function __construct()
    {
        $this->price = 0;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLocal(): ?Local
    {
        return $this->local;
    }

    public function setLocal(?Local $local): static
    {
        // unset the owning side of the relation if necessary
        if (null === $local && null !== $this->local) {
            $this->local->setLocalConstructiveAction(null);
        }

        // set the owning side of the relation if necessary
        if (null !== $local && $local->getLocalConstructiveAction() !== $this) {
            $local->setLocalConstructiveAction($this);
        }

        $this->local = $local;

        return $this;
    }

    public function getConstructiveAction(): ?ConstructiveAction
    {
        return $this->constructiveAction;
    }

    public function setConstructiveAction(?ConstructiveAction $constructiveAction): static
    {
        $this->constructiveAction = $constructiveAction;

        return $this;
    }

    public function getPrice(): int
    {
        return (is_null($this->price)) ? 0 : $this->price;
    }

    public function setPrice(?int $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getConstructiveSystem(): ?ConstructiveSystem
    {
        return $this->constructiveSystem;
    }

    public function setConstructiveSystem(?ConstructiveSystem $constructiveSystem): static
    {
        $this->constructiveSystem = $constructiveSystem;

        return $this;
    }
}
