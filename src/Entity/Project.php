<?php

namespace App\Entity;

use App\Entity\Enums\ProjectState;
use App\Entity\Enums\ProjectType;
use App\Entity\Traits\NameToStringTrait;
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

    #[ORM\Column]
    private bool $hasOccupiedArea;

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
    private ?\DateTimeImmutable $urbanRregulationAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $designAt = null;

    #[ORM\ManyToOne(inversedBy: 'projects')]
    private ?Client $client = null;

    #[ORM\OneToOne(inversedBy: 'project', cascade: ['persist', 'remove'])]
    private ?Contract $contract = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $comment = null;

    #[ORM\OneToOne(inversedBy: 'project', cascade: ['persist', 'remove'])]
    private ?Investment $investment = null;

    /**
     * @var Collection<int, DraftsmanProyect>
     */
    #[ORM\OneToMany(targetEntity: DraftsmanProyect::class, mappedBy: 'project', cascade: ['persist'])]
    private Collection $draftsmans;

    public function __construct()
    {
        $this->draftsmans = new ArrayCollection();
        $this->setState(ProjectState::Registered);
        $this->hasOccupiedArea = false;
        $this->registerAt = new \DateTimeImmutable();
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

        return $this;
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

    /**
     * @return Collection<int, DraftsmanProyect>
     */
    public function getDraftsmans(): Collection
    {
        return $this->draftsmans;
    }

    public function addDraftsman(DraftsmanProyect $draftsmanProyect): static
    {
        if (!$this->draftsmans->contains($draftsmanProyect)) {
            $this->draftsmans->add($draftsmanProyect);
        }

        return $this;
    }

    public function removeDraftsman(DraftsmanProyect $draftsmanProyect): static
    {
        $this->draftsmans->removeElement($draftsmanProyect);

        return $this;
    }

    public function hasOccupiedArea(): ?bool
    {
        return $this->hasOccupiedArea;
    }

    public function setHasOccupiedArea(bool $hasOccupiedArea): static
    {
        $this->hasOccupiedArea = $hasOccupiedArea;

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

    public function getUrbanRregulationAt(): ?\DateTimeImmutable
    {
        return $this->urbanRregulationAt;
    }

    public function setUrbanRregulationAt(?\DateTimeImmutable $urbanRregulationAt): static
    {
        $this->urbanRregulationAt = $urbanRregulationAt;

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

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): static
    {
        $this->comment = $comment;

        return $this;
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
}
