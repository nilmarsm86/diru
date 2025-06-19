<?php

namespace App\Entity;

use App\Entity\Traits\StartedAndFinishedTrait;
use App\Repository\ConstructorBuildingRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ConstructorBuildingRepository::class)]
class ConstructorBuilding
{
    use StartedAndFinishedTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'constructorBuildings')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\Valid]
    #[Assert\NotBlank(message: 'Establezca la constructora.')]
    private ?Constructor $constructor = null;

    #[ORM\ManyToOne(inversedBy: 'constructorBuildings')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\Valid]
    #[Assert\NotBlank(message: 'Establezca la obra.')]
    private ?Building $building = null;

    public function __construct()
    {
        $this->startedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getConstructor(): ?Constructor
    {
        return $this->constructor;
    }

    public function setConstructor(?Constructor $constructor): static
    {
        $this->constructor = $constructor;

        return $this;
    }

    public function getBuilding(): ?Building
    {
        return $this->building;
    }

    public function setBuilding(?Building $building): static
    {
        $this->building = $building;

        return $this;
    }

    public function hasBuilding(Building $building): bool
    {
        return $this->getBuilding()->getId() === $building->getId();
    }

    public function hasConstructor(Constructor $constructor): bool
    {
        return $this->getConstructor()->getId() === $constructor->getId();
    }
}
