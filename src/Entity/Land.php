<?php

namespace App\Entity;

use App\Repository\LandRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: LandRepository::class)]
class Land
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'El área de terreno está vacía.')]
    #[Assert\Positive(message: 'El area de terreno debe ser un número positivo.')]
    private ?float $landArea = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'El área ocupada está vacía.')]
    #[Assert\PositiveOrZero(message: 'El área ocupada debe ser positivo.')]
    //    #[Assert\Assert\LessThanOrEqual()]
    private ?float $occupiedArea = null;

    #[ORM\Column(nullable: true)]
    #[Assert\PositiveOrZero(message: 'El perímetro debe ser un número positivo.')]
    private ?float $perimeter = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $photo = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $microlocalization = null;

    #[ORM\Column]
    private ?int $floor = null;

    #[ORM\Column(nullable: true)]
    private ?bool $isBlocked = null;

    public function __construct()
    {
        $this->occupiedArea = 0;
        $this->floor = 0;
        $this->landArea = 1;
        $this->perimeter = 0;
        $this->isBlocked = false;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLandArea(): ?float
    {
        return $this->landArea;
    }

    public function setLandArea(float $landArea): static
    {
        $this->landArea = $landArea;

        return $this;
    }

    public function getOccupiedArea(): ?float
    {
        return $this->occupiedArea;
    }

    public function setOccupiedArea(float $occupiedArea): static
    {
        $this->occupiedArea = $occupiedArea;

        return $this;
    }

    public function getPerimeter(): ?float
    {
        return $this->perimeter;
    }

    public function setPerimeter(float $perimeter): static
    {
        $this->perimeter = $perimeter;

        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo): static
    {
        $this->photo = $photo;

        return $this;
    }

    public function getMicrolocalization(): ?string
    {
        return $this->microlocalization;
    }

    public function setMicrolocalization(?string $microlocalization): static
    {
        $this->microlocalization = $microlocalization;

        return $this;
    }

    public function getFloor(): ?int
    {
        return $this->floor;
    }

    public function setFloor(?int $floor): static
    {
        $this->floor = $floor;

        return $this;
    }

    public function hasOccupiedArea(): bool
    {
        return $this->occupiedArea > 0;
    }

    public function hasFloors(): bool
    {
        return $this->getFloor() > 0;
    }

    public function isBlocked(): ?bool
    {
        return $this->isBlocked;
    }

    public function setIsBlocked(bool $isBlocked): static
    {
        $this->isBlocked = $isBlocked;

        return $this;
    }
}
