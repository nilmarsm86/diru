<?php

namespace App\Entity;

use App\Entity\Enums\BuildingState;
use App\Entity\Enums\ProjectState;
use App\Entity\Enums\ProjectType;
use App\Entity\Traits\ClientTrait;
use App\Entity\Traits\NameToStringTrait;
use App\Repository\ProjectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProjectRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Project
{
    use NameToStringTrait;
    use ClientTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private string $type;

    #[Assert\Choice(
        choices: [ProjectType::Parcel, ProjectType::City],
        message: 'Seleccione un tipo de proyecto.'
    )]
    private ProjectType $enumType;

    #[ORM\Column(length: 255)]
    private string $state;

    #[Assert\Choice(
        choices: ProjectState::CHOICES,
        message: 'Seleccione un estado de proyecto.'
    )]
    private ProjectState $enumState;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $stopReason = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $registerAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $stoppedAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $canceledAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $initiatedAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $terrainDiagnosisAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $urbanRegulationAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $designAt = null;

    #[ORM\OneToOne(inversedBy: 'project', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: true)]
    private ?Contract $contract = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $comment = null;

    #[ORM\ManyToOne(cascade: ['persist'], inversedBy: 'projects')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\Valid]
    private ?Investment $investment = null;

    /**
     * @var Collection<int, Building>
     */
    #[ORM\OneToMany(targetEntity: Building::class, mappedBy: 'project', cascade: ['persist'])]
    #[Assert\Valid]
    #[ORM\OrderBy(['name' => 'ASC'])]
    private Collection $buildings;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[ORM\OrderBy(['name' => 'ASC'])]
    #[Assert\Valid]
    #[Assert\NotBlank(message: 'Seleccione la moneda de trabajo en el proyecto.')]
    private ?Currency $currency = null;

    /**
     * @var Collection<int, ProjectUrbanRegulation>
     */
    #[ORM\OneToMany(targetEntity: ProjectUrbanRegulation::class, mappedBy: 'project', cascade: ['persist'])]
    #[Assert\Valid]
    private Collection $projectUrbanRegulations;

    /**
     * @var Collection<int, DraftsmanProject>
     */
    #[ORM\OneToMany(targetEntity: DraftsmanProject::class, mappedBy: 'project', cascade: ['persist'])]
    #[Assert\Valid]
    private Collection $draftsmansProjects;

    public function __construct()
    {
        $this->setState(ProjectState::Registered);
        $this->setType(ProjectType::Parcel);
        $this->registerAt = new \DateTimeImmutable();
        $this->buildings = new ArrayCollection();
        $this->contract = null;
        $this->projectUrbanRegulations = new ArrayCollection();
        $this->draftsmansProjects = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ProjectType
    {
        return $this->enumType;
    }

    public function setType(ProjectType $enumType): static
    {
        $this->type = '';
        $this->enumType = $enumType;

        return $this;
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function onSave(): void
    {
        $this->type = $this->getType()->value;
        $this->state = $this->getState()->value;
        if (is_null($this->getId())) {
            if (is_null($this->getContract()) || is_null($this->getContract()->getCode())) {
                $this->setContract(null);
            }
        }
    }

    /**
     * @throws \Exception
     */
    #[ORM\PostLoad]
    public function onLoad(): void
    {
        $this->setType(ProjectType::from($this->type));
        $this->setState(ProjectState::from($this->state));
    }

    public function getState(): ProjectState
    {
        return $this->enumState;
    }

    public function setState(ProjectState $enumState): static
    {
        $this->state = '';
        $this->enumState = $enumState;

        if (ProjectState::Stopped === $enumState) {
            $this->stopAllBuildings();
        }

        return $this;
    }

    private function stopAllBuildings(): static
    {
        foreach ($this->getBuildings() as $building) {
            // se deben parar todas las obras del proyecto
            //            $building->setState(BuildingState::Stopped);
        }

        return $this;
    }

    public function isStopped(): bool
    {
        return ProjectState::Stopped === $this->getState();
    }

    public function getStopReason(): ?string
    {
        return $this->stopReason;
    }

    public function setStopReason(?string $stopReason): static
    {
        $this->stopReason = $stopReason;

        return $this;
    }

    public function getRegisterAt(): ?\DateTimeImmutable
    {
        return $this->registerAt;
    }

    public function setRegisterAt(?\DateTimeImmutable $registerAt): static
    {
        $this->registerAt = $registerAt;

        return $this;
    }

    public function getStoppedAt(): ?\DateTimeImmutable
    {
        return $this->stoppedAt;
    }

    public function setStoppedAt(?\DateTimeImmutable $stoppedAt): static
    {
        $this->stoppedAt = $stoppedAt;

        return $this;
    }

    public function getCanceledAt(): ?\DateTimeImmutable
    {
        return $this->canceledAt;
    }

    public function setCanceledAt(?\DateTimeImmutable $canceledAt): static
    {
        $this->canceledAt = $canceledAt;

        return $this;
    }

    public function getInitiatedAt(): ?\DateTimeImmutable
    {
        return $this->initiatedAt;
    }

    public function setInitiatedAt(?\DateTimeImmutable $initiatedAt): static
    {
        $this->initiatedAt = $initiatedAt;

        return $this;
    }

    public function getTerrainDiagnosisAt(): ?\DateTimeImmutable
    {
        return $this->terrainDiagnosisAt;
    }

    public function setTerrainDiagnosisAt(?\DateTimeImmutable $terrainDiagnosisAt): static
    {
        $this->terrainDiagnosisAt = $terrainDiagnosisAt;

        return $this;
    }

    public function getUrbanRegulationAt(): ?\DateTimeImmutable
    {
        return $this->urbanRegulationAt;
    }

    public function setUrbanRegulationAt(?\DateTimeImmutable $urbanRegulationAt): static
    {
        $this->urbanRegulationAt = $urbanRegulationAt;

        return $this;
    }

    public function getDesignAt(): ?\DateTimeImmutable
    {
        return $this->designAt;
    }

    public function setDesignAt(?\DateTimeImmutable $designAt): static
    {
        $this->designAt = $designAt;

        return $this;
    }

    public function getContract(): ?Contract
    {
        return $this->contract;
    }

    public function setContract(?Contract $contract): static
    {
        $this->contract = $contract;

        return $this;
    }

    public function hasContract(): bool
    {
        if (!is_null($this->getContract()) && !is_null($this->getContract()->getId())) {
            return true;
        }

        return false;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): static
    {
        $this->comment = $comment;

        return $this;
    }

    public function hasComment(): bool
    {
        return !is_null($this->getComment());
    }

    public function getInvestment(): ?Investment
    {
        return $this->investment;
    }

    public function setInvestment(?Investment $investment): static
    {
        $this->investment = $investment;

        return $this;
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
            $building->setProject($this);
        }

        return $this;
    }

    public function removeBuilding(Building $building): static
    {
        if ($this->buildings->removeElement($building)) {
            // set the owning side to null (unless already changed)
            if ($building->getProject() === $this) {
                $building->setProject(null);
            }
        }

        return $this;
    }

    public function hasBuildings(): bool
    {
        return $this->getBuildings()->count() > 0;
    }

    public function getBuildingsAmount(): int
    {
        return $this->getBuildings()->count();
    }

    public function getCurrency(): ?Currency
    {
        return $this->currency;
    }

    public function setCurrency(?Currency $currency): static
    {
        $this->currency = $currency;

        return $this;
    }

    public function createAutomaticInvestment(Municipality $municipality): static
    {
        $investment = new Investment();
        $investment->setName('Inversión del proyecto '.$this->getName());
        $investment->setStreet('Direccion de la inversión');
        $investment->setMunicipality($municipality);

        $this->setInvestment($investment);

        return $this;
    }

    public function createAutomaticBuilding(): static
    {
        $building = new Building();
        $building->setName('Obra del proyecto '.$this->getName());
        $this->addBuilding($building);

        return $this;
    }

    public function cancel(): void
    {
        $this->setState(ProjectState::Canceled);
        foreach ($this->getBuildings() as $building) {
            //            $building->cancel();
        }
    }

    /**
     * @return Collection<int, ProjectUrbanRegulation>
     */
    public function getProjectUrbanRegulations(): Collection
    {
        return $this->projectUrbanRegulations;
    }

    public function addProjectUrbanRegulation(ProjectUrbanRegulation $projectUrbanRegulation): static
    {
        if (!$this->projectUrbanRegulations->contains($projectUrbanRegulation)) {
            $this->projectUrbanRegulations->add($projectUrbanRegulation);
        }

        return $this;
    }

    public function removeProjectUrbanRegulation(ProjectUrbanRegulation $projectUrbanRegulation): static
    {
        $this->projectUrbanRegulations->removeElement($projectUrbanRegulation);

        return $this;
    }

    public function getPrice(?bool $original = null): int|float
    {
        if (0 === $this->getBuildingsAmount()) {
            return 0;
        }

        $price = 0;
        /** @var Building $building */
        foreach ($this->getBuildings() as $building) {
            $price += $building->getPrice();
        }

        return $price;
    }

    /**
     * @return Collection<int, Draftsman>
     */
    public function getDraftsmans(): Collection
    {
        $draftsman = new ArrayCollection();
        foreach ($this->getDraftsmansProjects() as $draftsmansProject) {
            $draftsman->add($draftsmansProject->getDraftsman());
        }

        return $draftsman;
    }

    public function getActiveDraftsman(): ?Draftsman
    {
        foreach ($this->getDraftsmansProjects() as $draftsmansProject) {
            if (is_null($draftsmansProject->getFinishedAt())) {
                return $draftsmansProject->getDraftsman();
            }
        }

        return null;
    }

    public function addDraftsman(Draftsman $draftsman): static
    {
        $actualDraftsman = $this->getActiveDraftsman();
        if (!is_null($actualDraftsman)) {
            if ($actualDraftsman->getId() !== $draftsman->getId()) {
                $actualDraftsmanProject = $actualDraftsman->getDraftsmanProjectByProject($this);
                $actualDraftsmanProject?->setFinishedAt(new \DateTimeImmutable());

                $draftsmanProject = new DraftsmanProject();
                $draftsmanProject->setProject($this);
                $draftsmanProject->setDraftsman($draftsman);

                $this->addDraftsmanProject($draftsmanProject);
            }
        } else {
            $draftsmanProject = new DraftsmanProject();
            $draftsmanProject->setProject($this);
            $draftsmanProject->setDraftsman($draftsman);

            $this->addDraftsmanProject($draftsmanProject);
        }

        return $this;
    }

    public function removeDraftsman(Draftsman $draftsman): static
    {
        $draftsmansProjects = $draftsman->getDraftsmansProjects();
        foreach ($draftsmansProjects as $draftsmansProject) {
            if ($draftsmansProject->hasProject($this)) {
                $this->removeDraftsmansProject($draftsmansProject);

                return $this;
            }
        }

        return $this;
    }

    public function hasDraftsman(): bool
    {
        return $this->getDraftsmans()->count() > 0;
    }

    /**
     * @return Collection<int, DraftsmanProject>
     */
    public function getDraftsmansProjects(): Collection
    {
        return $this->draftsmansProjects;
    }

    public function addDraftsmanProject(DraftsmanProject $draftsmanProject): static
    {
        if (!$this->draftsmansProjects->contains($draftsmanProject)) {
            $this->draftsmansProjects->add($draftsmanProject);
        }

        return $this;
    }

    public function removeDraftsmansProject(DraftsmanProject $draftsmanProject): static
    {
        $this->draftsmansProjects->removeElement($draftsmanProject);

        return $this;
    }
}
