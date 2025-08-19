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
    #[Assert\PositiveOrZero(message: 'El valor debe ser positivo')]
    private ?int $price;

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
        if ($local === null && $this->local !== null) {
            $this->local->setLocalConstructiveAction(null);
        }

        // set the owning side of the relation if necessary
        if ($local !== null && $local->getLocalConstructiveAction() !== $this) {
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

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(?int $price): static
    {
        $this->price = $price;

        return $this;
    }
}
