<?php

namespace App\Entity;

use App\Entity\Traits\StartedAndFinishedTrait;
use App\Repository\CorporateEntityBuildingRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CorporateEntityBuildingRepository::class)]
class CorporateEntityBuilding
{
    use StartedAndFinishedTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(cascade: ['persist'], inversedBy: 'corporateEntityBuildings')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\Valid]
    #[Assert\NotBlank(message: 'Establezca la entidad corporativa de tipo constructora.')]
    private ?CorporateEntity $corporateEntity = null;

    #[ORM\ManyToOne(inversedBy: 'corporateEntityBuildings')]
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

    public function getCorporateEntity(): ?CorporateEntity
    {
        return $this->corporateEntity;
    }

    public function setCorporateEntity(?CorporateEntity $corporateEntity): static
    {
        $this->corporateEntity = $corporateEntity;

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

    public function hasCorporateEntity(CorporateEntity $corporateEntity): bool
    {
        return $this->getCorporateEntity()?->getId() === $corporateEntity->getId();
    }
}
