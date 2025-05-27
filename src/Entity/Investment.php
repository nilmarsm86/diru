<?php

namespace App\Entity;

use App\Entity\Traits\AddressTrait;
use App\Entity\Traits\NameToStringTrait;
use App\Repository\InvestmentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InvestmentRepository::class)]
class Investment
{
    use AddressTrait;
    use NameToStringTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $betweenStreets = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $town = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $popularCouncil = null;

    #[ORM\ManyToOne(inversedBy: 'investments')]
    #[ORM\JoinColumn(nullable: true)]
    private ?LocationZone $locationZone = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $block = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $district = null;

    #[ORM\Column(length: 255)]
    private ?string $street = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $addressNumber = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Municipality $municipality = null;

    /**
     * @var Collection<int, Building>
     */
    #[ORM\OneToMany(targetEntity: Building::class, mappedBy: 'investment')]
    private Collection $buildings;

    #[ORM\OneToOne(mappedBy: 'investment', cascade: ['persist', 'remove'])]
    private ?Project $project = null;

    public function __construct()
    {
        $this->buildings = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBetweenStreets(): ?string
    {
        return $this->betweenStreets;
    }

    public function setBetweenStreets(?string $betweenStreets): static
    {
        $this->betweenStreets = $betweenStreets;

        return $this;
    }

    public function getTown(): ?string
    {
        return $this->town;
    }

    public function setTown(?string $town): static
    {
        $this->town = $town;

        return $this;
    }

    public function getPopularCouncil(): ?string
    {
        return $this->popularCouncil;
    }

    public function setPopularCouncil(?string $popularCouncil): static
    {
        $this->popularCouncil = $popularCouncil;

        return $this;
    }

    public function getLocationZone(): ?LocationZone
    {
        return $this->locationZone;
    }

    public function setLocationZone(?LocationZone $locationZone): static
    {
        $this->locationZone = $locationZone;

        return $this;
    }

    public function getBlock(): ?string
    {
        return $this->block;
    }

    public function setBlock(?string $block): static
    {
        $this->block = $block;

        return $this;
    }

    public function getDistrict(): ?string
    {
        return $this->district;
    }

    public function setDistrict(?string $district): static
    {
        $this->district = $district;

        return $this;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function setStreet(string $street): static
    {
        $this->street = $street;

        return $this;
    }

    public function getAddressNumber(): ?string
    {
        return $this->addressNumber;
    }

    public function setAddressNumber(?string $addressNumber): static
    {
        $this->addressNumber = $addressNumber;

        return $this;
    }

    public function getLocationZoneName(): string
    {
        if(!is_null($this->getLocationZone())){
            return $this->getLocationZone()->getName();
        }

        return "";
    }

    /**
     * @return Collection<int, Building>
     */
    public function getBuildings(): Collection
    {
        return $this->buildings;
    }

    public function addBuilding(Building $building): static
    {
        if (!$this->buildings->contains($building)) {
            $this->buildings->add($building);
            $building->setInvestment($this);
        }

        return $this;
    }

    public function removeBuilding(Building $building): static
    {
        if ($this->buildings->removeElement($building)) {
            // set the owning side to null (unless already changed)
            if ($building->getInvestment() === $this) {
                $building->setInvestment(null);
            }
        }

        return $this;
    }

    public function getBuildingsAmount(): int
    {
        return $this->getBuildings()->count();
    }

    public function getProject(): ?Project
    {
        return $this->project;
    }

    public function setProject(?Project $project): static
    {
        // unset the owning side of the relation if necessary
        if ($project === null && $this->project !== null) {
            $this->project->setInvestment(null);
        }

        // set the owning side of the relation if necessary
        if ($project !== null && $project->getInvestment() !== $this) {
            $project->setInvestment($this);
        }

        $this->project = $project;

        return $this;
    }

}
