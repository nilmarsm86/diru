<?php

namespace App\Entity;

use App\Entity\Traits\StartedAndFinishedTrait;
use App\Repository\DraftsmanBuildingRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: DraftsmanBuildingRepository::class)]
class DraftsmanBuilding
{
    use StartedAndFinishedTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'draftsmansBuildings')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\Valid]
    #[Assert\NotBlank(message: 'Establezca el proyectista.')]
    private ?Draftsman $draftsman = null;

    #[ORM\ManyToOne(inversedBy: 'draftsmansBuildings')]
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

    public function getDraftsman(): ?Draftsman
    {
        return $this->draftsman;
    }

    public function setDraftsman(?Draftsman $draftsman): static
    {
        $this->draftsman = $draftsman;

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
        return $this->getBuilding()?->getId() === $building->getId();
    }

    public function hasDraftsman(Draftsman $draftsman): bool
    {
        return $this->getDraftsman()?->getId() === $draftsman->getId();
    }
}
