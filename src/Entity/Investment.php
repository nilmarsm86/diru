<?php

namespace App\Entity;

use App\Entity\Enums\ProjectType;
use App\Entity\Traits\AddressTrait;
use App\Entity\Traits\NameToStringTrait;
use App\Repository\InvestmentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

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
    #[Assert\Valid]
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
    #[Assert\Valid]
//    #[Assert\NotBlank(message: 'Establezca el municipio.')]
    private ?Municipality $municipality = null;

    /** @var Collection<int, Project>  */
    #[ORM\OneToMany(targetEntity: Project::class, mappedBy: 'investment')]
    #[Assert\Valid]
    private Collection $projects;

    public function __construct()
    {
        $this->projects = new ArrayCollection();
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
     * @return Collection<int, Project>
     */
    public function getProjects(): Collection
    {
        return $this->projects;
    }

    public function addProject(Project $project): static
    {
        if (!$this->projects->contains($project)) {
            $this->projects->add($project);
            $project->setInvestment($this);
        }

        return $this;
    }

    public function removeProject(Project $project): static
    {
        if ($this->projects->removeElement($project)) {
            // set the owning side to null (unless already changed)
            if ($project->getInvestment() === $this) {
                $project->setInvestment(null);
            }
        }

        return $this;
    }

    public function getBuildingsAmount(): int
    {
        $totalBuildingsAmount = 0;
        foreach ($this->getProjects() as $project){
            $totalBuildingsAmount += $project->getBuildingsAmount();
        }
        return $totalBuildingsAmount;
    }

    public function hasProjects(): bool
    {
        return $this->getProjects()->count() > 0;
    }

}
