<?php

namespace App\Entity;

use App\Entity\Enums\BuildingState;
use App\Entity\Enums\ProjectState;
use App\Entity\Enums\ProjectType;
use App\Entity\Traits\NameToStringTrait;
use App\Repository\EnterpriseClientRepository;
use App\Repository\IndividualClientRepository;
use App\Repository\ProjectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProjectRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Project
{
    use NameToStringTrait;

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

    #[ORM\ManyToOne(inversedBy: 'projects')]
    #[Assert\Valid]
//    #[Assert\NotBlank(message: 'Seleccione o cree un cliente para el proyecto.')]
    private ?Client $client = null;

    #[ORM\OneToOne(inversedBy: 'project', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: true)]
//    #[Assert\Valid]
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
    #[ORM\OrderBy(["name" => "ASC"])]
    private Collection $buildings;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[ORM\OrderBy(["name" => "ASC"])]
    #[Assert\Valid]
    #[Assert\NotBlank(message: 'Seleccione la moneda de trabajo en el proyecto.')]
    private ?Currency $currency = null;

    public function __construct()
    {
        $this->setState(ProjectState::Registered);
        $this->setType(ProjectType::Parcel);
        $this->registerAt = new \DateTimeImmutable();
        $this->buildings = new ArrayCollection();
        $this->contract = null;
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
        $this->type = "";
        $this->enumType = $enumType;

        return $this;
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function onSave(): void
    {
        $this->type = $this->getType()->value;
        $this->state = $this->getState()->value;
        if(is_null($this->getId())){
            if (is_null($this->getContract()) || is_null($this->getContract()->getCode())) {
                $this->setContract(null);
            }
        }
    }

    /**
     * @throws Exception
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
        $this->state = "";
        $this->enumState = $enumState;

        if($enumState === ProjectState::Stopped){
            $this->stopAllBuildings();
        }

        return $this;
    }

    private function stopAllBuildings(): static
    {
        foreach ($this->getBuildings() as $building){
            $building->setState(BuildingState::Stopped);
        }

        return $this;
    }

    public function isStopped(): bool
    {
        return $this->getState() === ProjectState::Stopped;
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

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function isIndividualClient(IndividualClientRepository $individualClientRepository): bool
    {
        if(is_null($this->getId())){
            return true;
        }

        $client = $this->getClient();
        if (!is_null($client)) {
            $individual = $individualClientRepository->find($client->getId());
            return !is_null($individual);
        }

        return false;
    }

    public function getIndividualClient(IndividualClientRepository $individualClientRepository): ?IndividualClient
    {
        $client = $this->getClient();
        if (!is_null($client)) {
            return $individualClientRepository->find($client->getId());
        }

        return null;
    }

    public function isEnterpriseClient(EnterpriseClientRepository $enterpriseClientRepository): bool
    {
        $client = $this->getClient();
        if (!is_null($client)) {
            $enterprise = $enterpriseClientRepository->find($client->getId());
            return !is_null($enterprise);
        }

        return false;
    }

    public function getEnterpriseClient(EnterpriseClientRepository $enterpriseClientRepository): ?EnterpriseClient
    {
        $client = $this->getClient();
        if (!is_null($client)) {
            return $enterpriseClientRepository->find($client->getId());
        }

        return null;
    }

    public function setClient(?Client $client): static
    {
        $this->client = $client;

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

    public function isFromEnterpriseClient(): bool
    {
        return $this->getClient() instanceof EnterpriseClient;
    }

    public function isFromIndividualClient(): bool
    {
        return $this->getClient() instanceof IndividualClient;
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
        $investment->setName('Inversión del proyecto ' . $this->getName());
        $investment->setStreet('Direccion de la inversión');
        $investment->setMunicipality($municipality);

        $this->setInvestment($investment);

        return $this;
    }

    public function createAutomaticBuilding(): static
    {
        $building = new Building();
        $building->setName('Obra del proyecto ' . $this->getName());
        $this->addBuilding($building);

        return $this;
    }

    public function cancel(): void
    {
        $this->setState(ProjectState::Canceled);
        foreach ($this->getBuildings() as $building){
            $building->cancel();
        }
    }


}
