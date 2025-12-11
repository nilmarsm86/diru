<?php

namespace App\Entity;

use App\Entity\Enums\BuildingState;
use App\Entity\Interfaces\MeasurementDataInterface;
// use App\Entity\Traits\HasReplyTrait;
use App\Entity\Traits\MeasurementDataTrait;
use App\Entity\Traits\NameToStringTrait;
use App\Repository\BuildingRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: BuildingRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[DoctrineAssert\UniqueEntity(fields: ['name', 'project'], message: 'Ya existe en el proyecto una obra con este nombre.', errorPath: 'name')]
class Building implements MeasurementDataInterface
{
    use NameToStringTrait;
    use MeasurementDataTrait;

    //    use HasReplyTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private string $state;

    #[Assert\Choice(
        choices: BuildingState::CHOICES,
        message: 'Seleccione un estado de obra.'
    )]
    private BuildingState $enumState;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $stopReason = null;

    //    #[ORM\Column(type: Types::BIGINT)]
    //    #[Assert\PositiveOrZero(message: 'El valor debe ser positivo')]
    //    private ?int $estimatedValueConstruction = 0;

    #[ORM\Column(type: Types::BIGINT)]
    #[Assert\PositiveOrZero(message: 'El valor debe ser positivo')]
    private ?int $estimatedValueEquipment = 0;

    #[ORM\Column(type: Types::BIGINT)]
    #[Assert\PositiveOrZero(message: 'El valor debe ser positivo')]
    private ?int $estimatedValueOther = 0;

    #[ORM\Column(type: Types::BIGINT)]
    #[Assert\PositiveOrZero(message: 'El valor debe ser positivo')]
    private ?int $approvedValueConstruction = 0;

    #[ORM\Column(type: Types::BIGINT)]
    #[Assert\PositiveOrZero(message: 'El valor debe ser positivo')]
    private ?int $approvedValueEquipment = 0;

    #[ORM\Column(type: Types::BIGINT)]
    #[Assert\PositiveOrZero(message: 'El valor debe ser positivo')]
    private ?int $approvedValueOther = 0;

    #[ORM\Column(type: Types::BIGINT)]
    #[Assert\PositiveOrZero(message: 'El valor debe ser positivo')]
    private ?int $projectPriceTechnicalPreparation = 0;

    #[ORM\ManyToOne(inversedBy: 'buildings')]
    #[ORM\JoinColumn(nullable: true)]
    #[Assert\Valid]
    #[Assert\NotBlank(message: 'Seleccione o cree el proyecto al cual pertenece la obra.')]
    private ?Project $project = null;

    /**
     * @var Collection<int, DraftsmanBuilding>
     */
    #[ORM\OneToMany(targetEntity: DraftsmanBuilding::class, mappedBy: 'building', cascade: ['persist'])]
    #[Assert\Valid]
    private Collection $draftsmansBuildings;

    /**
     * @var Collection<int, ConstructorBuilding>
     */
    #[ORM\OneToMany(targetEntity: ConstructorBuilding::class, mappedBy: 'building', cascade: ['persist'])]
    #[Assert\Valid]
    private Collection $constructorBuildings;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[Assert\Valid]
    private ?Land $land = null;

    /**
     * @var Collection<int, Floor>
     */
    #[ORM\OneToMany(targetEntity: Floor::class, mappedBy: 'building', cascade: ['persist', 'remove'])]
    private Collection $floors;

    #[ORM\Column(nullable: true)]
    private ?bool $isNew = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'Establezca la cantidad de personas.')]
    #[Assert\Positive(message: 'El valor debe ser positivo')]
    private ?int $population = 1;

    #[ORM\Column(type: Types::BIGINT)]
    #[Assert\PositiveOrZero(message: 'El valor debe ser positivo')]
    private ?int $constructionAssembly = 0;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $constructionAssemblyComment = null;

    /**
     * @var Collection<int, LandNetworkConnection>
     */
    #[ORM\OneToMany(targetEntity: LandNetworkConnection::class, mappedBy: 'building', cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[Assert\Valid]
    private Collection $landNetworkConnections;

    #[ORM\Column(nullable: true)]
    private ?bool $hasReply = null;

    /**
     * @var Collection<int, UrbanizationEstimate>
     */
    #[ORM\OneToMany(targetEntity: UrbanizationEstimate::class, mappedBy: 'building', cascade: ['persist'])]
    private Collection $urbanizationEstimates;

    /**
     * @var Collection<int, ProjectTechnicalPreparationEstimate>
     */
    #[ORM\OneToMany(targetEntity: ProjectTechnicalPreparationEstimate::class, mappedBy: 'building', cascade: ['persist'])]
    private Collection $projectTechnicalPreparationEstimates;

    /**
     * @var Collection<int, BuildingSeparateConcept>
     */
    #[ORM\OneToMany(targetEntity: BuildingSeparateConcept::class, mappedBy: 'building', cascade: ['persist'])]
    private Collection $buildingSeparateConcepts;

    public function __construct()
    {
        //        $this->estimatedValueConstruction = 0;
        $this->estimatedValueEquipment = 0;
        $this->estimatedValueOther = 0;

        $this->approvedValueConstruction = 0;
        $this->approvedValueEquipment = 0;
        $this->approvedValueOther = 0;

        $this->projectPriceTechnicalPreparation = 0;

        $this->setState(BuildingState::Registered);
        $this->draftsmansBuildings = new ArrayCollection();
        $this->constructorBuildings = new ArrayCollection();
        $this->floors = new ArrayCollection();

        //        $this->isNew = false;

        $this->hasReply = false;

        $this->population = 1;
        $this->constructionAssembly = 0;
        $this->landNetworkConnections = new ArrayCollection();
        $this->urbanizationEstimates = new ArrayCollection();
        $this->projectTechnicalPreparationEstimates = new ArrayCollection();
        $this->buildingSeparateConcepts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function onSave(): void
    {
        $this->state = $this->getState()->value;
    }

    /**
     * @throws \Exception
     */
    #[ORM\PostLoad]
    public function onLoad(): void
    {
        $this->setState(BuildingState::from($this->state));
    }

    public function getState(): BuildingState
    {
        return $this->enumState;
    }

    public function setState(BuildingState $enumState): static
    {
        $this->state = '';
        $this->enumState = $enumState;

        return $this;
    }

    public function isStopped(): bool
    {
        return BuildingState::Stopped === $this->getState();
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

    //    public function getEstimatedValueConstruction(): int|float
    //    {
    //        return $this->getPrice();
    //        //return $this->estimatedValueConstruction;
    //    }
    //
    //    public function setEstimatedValueConstruction(?int $estimatedValueConstruction): static
    //    {
    //        $this->estimatedValueConstruction = $estimatedValueConstruction;
    //
    //        return $this;
    //    }

    public function getEstimatedValueEquipment(): ?int
    {
        return $this->estimatedValueEquipment;
    }

    public function setEstimatedValueEquipment(?int $estimatedValueEquipment): static
    {
        $this->estimatedValueEquipment = $estimatedValueEquipment;

        return $this;
    }

    public function getEstimatedValueOther(): ?int
    {
        return $this->estimatedValueOther;
    }

    public function setEstimatedValueOther(?int $estimatedValueOther): static
    {
        $this->estimatedValueOther = $estimatedValueOther;

        return $this;
    }

    public function getEstimatedValueUrbanization(): ?int
    {
        // sumatoria de cada uno de los items de estimados de urbanizacion
        return 0;
    }

    public function getApprovedValueConstruction(): ?int
    {
        return $this->approvedValueConstruction;
    }

    public function setApprovedValueConstruction(?int $approvedValueConstruction): static
    {
        $this->approvedValueConstruction = $approvedValueConstruction;

        return $this;
    }

    public function getApprovedValueEquipment(): ?int
    {
        return $this->approvedValueEquipment;
    }

    public function setApprovedValueEquipment(?int $approvedValueEquipment): static
    {
        $this->approvedValueEquipment = $approvedValueEquipment;

        return $this;
    }

    public function getApprovedValueOther(): ?int
    {
        return $this->approvedValueOther;
    }

    public function setApprovedValueOther(?int $approvedValueOther): static
    {
        $this->approvedValueOther = $approvedValueOther;

        return $this;
    }

    public function getProjectPriceTechnicalPreparation(): ?int
    {
        return $this->projectPriceTechnicalPreparation;
    }

    public function setProjectPriceTechnicalPreparation(?int $projectPriceTechnicalPreparation): static
    {
        $this->projectPriceTechnicalPreparation = $projectPriceTechnicalPreparation;

        return $this;
    }

    public function getTotalEstimatedValue(): int|float
    {
        return $this->getPrice() + $this->getEstimatedValueEquipment() + $this->getEstimatedValueOther() + $this->projectPriceTechnicalPreparation;
    }

    public function getTotalApprovedValue(): ?int
    {
        return $this->getApprovedValueConstruction() + $this->getApprovedValueEquipment() + $this->getApprovedValueOther();
    }

    // debo convertir el dinero en centavos, valores estimados y valores aprobados

    public function getProject(): ?Project
    {
        return $this->project;
    }

    public function setProject(?Project $project): static
    {
        $this->project = $project;

        return $this;
    }

    /**
     * @return Collection<int, Draftsman>
     */
    public function getDraftsmans(): Collection
    {
        $draftsman = new ArrayCollection();
        foreach ($this->getDraftsmansBuildings() as $draftsmansBuilding) {
            $draftsman->add($draftsmansBuilding->getDraftsman());
        }

        return $draftsman;
    }

    public function getActiveDraftsman(): ?Draftsman
    {
        foreach ($this->getDraftsmansBuildings() as $draftsmansBuilding) {
            if (is_null($draftsmansBuilding->getFinishedAt())) {
                return $draftsmansBuilding->getDraftsman();
            }
        }

        return null;
    }

    public function addDraftsman(Draftsman $draftsman): static
    {
        $actualDraftsman = $this->getActiveDraftsman();
        if (!is_null($actualDraftsman)) {
            if ($actualDraftsman->getId() !== $draftsman->getId()) {
                $actualDraftsmanBuilding = $actualDraftsman->getDraftsmanBuildingByBuilding($this);
                $actualDraftsmanBuilding?->setFinishedAt(new \DateTimeImmutable());

                $draftsmanBuilding = new DraftsmanBuilding();
                $draftsmanBuilding->setBuilding($this);
                $draftsmanBuilding->setDraftsman($draftsman);

                $this->addDraftsmanBuilding($draftsmanBuilding);
            }
        } else {
            $draftsmanBuilding = new DraftsmanBuilding();
            $draftsmanBuilding->setBuilding($this);
            $draftsmanBuilding->setDraftsman($draftsman);

            $this->addDraftsmanBuilding($draftsmanBuilding);
        }

        return $this;
    }

    public function removeDraftsman(Draftsman $draftsman): static
    {
        $draftsmansBuildings = $draftsman->getDraftsmansBuildings();
        foreach ($draftsmansBuildings as $draftsmanBuilding) {
            if ($draftsmanBuilding->hasBuilding($this)) {
                $this->removeDraftsmansBuilding($draftsmanBuilding);

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
     * @return Collection<int, DraftsmanBuilding>
     */
    public function getDraftsmansBuildings(): Collection
    {
        return $this->draftsmansBuildings;
    }

    public function addDraftsmanBuilding(DraftsmanBuilding $draftsmanBuilding): static
    {
        if (!$this->draftsmansBuildings->contains($draftsmanBuilding)) {
            $this->draftsmansBuildings->add($draftsmanBuilding);
        }

        return $this;
    }

    public function removeDraftsmansBuilding(DraftsmanBuilding $draftsmanBuilding): static
    {
        $this->draftsmansBuildings->removeElement($draftsmanBuilding);

        return $this;
    }

    /**
     * @return Collection<int, Constructor>
     */
    public function getConstructors(): Collection
    {
        $constrcutors = new ArrayCollection();
        /** @var ConstructorBuilding $constructorBuilding */
        foreach ($this->getConstructorBuildings() as $constructorBuilding) {
            $constrcutors->add($constructorBuilding->getConstructor());
        }

        return $constrcutors;
    }

    public function getActiveConstructor(): ?Constructor
    {
        /** @var ConstructorBuilding $constructorBuilding */
        foreach ($this->getConstructorBuildings() as $constructorBuilding) {
            if (is_null($constructorBuilding->getFinishedAt())) {
                return $constructorBuilding->getConstructor();
            }
        }

        return null;
    }

    public function addConstructor(Constructor $constructor): static
    {
        $actualConstructor = $this->getActiveConstructor();
        if (!is_null($actualConstructor)) {
            if ($actualConstructor->getId() !== $constructor->getId()) {
                $actualConstrcutorBuilding = $actualConstructor->getConstructorBuildingByBuilding($this);
                $actualConstrcutorBuilding?->setFinishedAt(new \DateTimeImmutable());

                $constructorBuilding = new ConstructorBuilding();
                $constructorBuilding->setBuilding($this);
                $constructorBuilding->setConstructor($constructor);

                $this->addConstructorBuilding($constructorBuilding);
            }
        } else {
            $constructorBuilding = new ConstructorBuilding();
            $constructorBuilding->setBuilding($this);
            $constructorBuilding->setConstructor($constructor);

            $this->addConstructorBuilding($constructorBuilding);
        }

        return $this;
    }

    public function removeConstructor(Constructor $constructor): static
    {
        $constructorBuildings = $constructor->getConstructorBuildings();
        foreach ($constructorBuildings as $constructorBuilding) {
            if ($constructorBuilding->hasBuilding($this)) {
                $this->removeConstructorBuilding($constructorBuilding);

                return $this;
            }
        }

        return $this;
    }

    public function hasConstructor(): bool
    {
        return $this->getConstructors()->count() > 0;
    }

    public function hasActiveConstructor(): bool
    {
        return !is_null($this->getActiveConstructor());
    }

    public function getActiveConstructorName(): ?string
    {
        return $this->getActiveConstructor()?->getName();
    }

    public function getActiveConstructorId(): ?int
    {
        return $this->getActiveConstructor()?->getId();
    }

    /**
     * @return Collection<int, ConstructorBuilding>
     */
    public function getConstructorBuildings(): Collection
    {
        return $this->constructorBuildings;
    }

    public function addConstructorBuilding(ConstructorBuilding $constructorBuilding): static
    {
        if (!$this->constructorBuildings->contains($constructorBuilding)) {
            $this->constructorBuildings->add($constructorBuilding);
        }

        return $this;
    }

    public function removeConstructorBuilding(ConstructorBuilding $constructorBuilding): static
    {
        $this->constructorBuildings->removeElement($constructorBuilding);

        return $this;
    }

    public function getLand(): ?Land
    {
        return $this->land;
    }

    public function setLand(?Land $land): static
    {
        $this->land = $land;

        return $this;
    }

    /**
     * @return Collection<int, Floor>
     */
    public function getFloors(): Collection
    {
        return $this->floors;
    }

    /**
     * @return ArrayCollection<int, Floor>
     */
    public function getOriginalFloors(): ArrayCollection
    {
        $originalFloors = new ArrayCollection();
        foreach ($this->getFloors() as $floor) {
            if ($floor->isOriginal()) {
                $originalFloors->add($floor);
            }
        }

        return $originalFloors;
    }

    /**
     * @return ArrayCollection<int, Floor>
     */
    public function getOriginalExistsFloors(): ArrayCollection
    {
        $originalFloors = new ArrayCollection();
        foreach ($this->getFloors() as $floor) {
            if ($floor->isOriginal() && !is_null($floor->getId())) {
                $originalFloors->add($floor);
            }
        }

        return $originalFloors;
    }

    /**
     * @return ArrayCollection<int, Floor>
     */
    public function getReplyFloors(): ArrayCollection
    {
        $replyFloors = new ArrayCollection();
        foreach ($this->getFloors() as $floor) {
            if (!$floor->isOriginal()) {
                $replyFloors->add($floor);
            }
        }

        return $replyFloors;
    }

    /**
     * @return ArrayCollection<int, Floor>
     */
    public function getReplyExistsFloors(): ArrayCollection
    {
        $replyFloors = new ArrayCollection();
        foreach ($this->getFloors() as $floor) {
            if (!$floor->isOriginal() && !is_null($floor->getId())) {
                $replyFloors->add($floor);
            }
        }

        return $replyFloors;
    }

    public function addFloor(Floor $floor): static
    {
        if (!$this->floors->contains($floor)) {
            $this->floors->add($floor);
            $floor->setBuilding($this);
        }

        return $this;
    }

    public function removeFloor(Floor $floor): static
    {
        if ($this->floors->removeElement($floor)) {
            // set the owning side to null (unless already changed)
            if ($floor->getBuilding() === $this) {
                $floor->setBuilding(null);
            }
        }

        return $this;
    }

    public function hasFloors(): bool
    {
        return $this->getFloorAmount() > 0;
    }

    public function hasOriginalFloors(): bool
    {
        return $this->getOriginalFloors()->count() > 0;
    }

    public function hasReplyFloors(): bool
    {
        return $this->getReplyFloors()->count() > 0;
    }

    public function isBuildingNew(): bool
    {
        return false === $this->hasOriginalFloors();
    }

    public function cancel(): static
    {
        $this->setState(BuildingState::Canceled);

        return $this;
    }

    public function getLandArea(): int|float|null
    {
        return $this->getLand()?->getLandArea();
    }

    public function getOccupiedArea(): ?float
    {
        // TODO: esto esta mal, debe ser por sumatoria tambien de sus elementos, pues si se derrumba algo el numero no es real
        return $this->getLand()?->getOccupiedArea();
    }

    /*
     * Create the automatic the floors, based on land floors
     */
    public function createFloors(bool $reply = false, ?EntityManagerInterface $entityManager = null): static
    {
        $floor = $this->getLand()?->getFloor();
        $this->createAutomaticFloor('Planta Baja', true, 0, $reply, $entityManager);

        if ($floor > 1) {
            for ($i = 1; $i < $floor; ++$i) {
                $this->createAutomaticFloor('Planta '.$i, false, $i, $reply, $entityManager);
            }
        }

        return $this;
    }

    private function createAutomaticFloor(string $name, bool $isGroundFloor = false, int $position = 0, bool $reply = false, ?EntityManagerInterface $entityManager = null): void
    {
        Floor::createAutomatic(null, $this, $name, $isGroundFloor, $position, $reply, $entityManager);
    }

    public function isNew(): ?bool
    {
        return $this->isNew;
    }

    public function setIsNew(bool $isNew): static
    {
        $this->isNew = $isNew;

        return $this;
    }

    public function getMaxArea(): ?float
    {
        return ($this->isNew) ? $this->getLandArea() : $this->getOccupiedArea();
    }

    public function shortName(): ?string
    {
        if (strlen($this->getName()) > 50) {
            return substr($this->getName(), 0, 50).'...';
        }

        return $this->getName();
    }

    public function getMeasurementData(string $method, ?bool $original = null): int|float
    {
        if (is_null($original)) {
            $floors = (!$this->hasReply()) ? $this->getOriginalFloors() : $this->getReplyFloors();
            $original = !$this->hasReply();
        } else {
            $floors = ($original) ? $this->getOriginalFloors() : $this->getReplyFloors();
        }

        $data = 0;
        foreach ($floors as $floor) {
            $callback = [$floor, $method];
            assert(is_callable($callback));
            $data += call_user_func($callback, $original);
        }

        return $data;
    }

    public function getUnassignedArea(?bool $original = null): ?float
    {
        return $this->getMeasurementData('getUnassignedArea', $original);
    }

    public function getFreeArea(?bool $original = null): ?float
    {
        return $this->getMeasurementData('getFreeArea', $original);
    }

    public function hasFreeArea(): bool
    {
        return $this->getFreeArea() > 0;
    }

    public function hasUnassignedArea(): bool
    {
        return $this->getUnassignedArea() > 0;
    }

    public function getMaxHeight(?bool $original = null): float
    {
        if (is_null($original)) {
            $floors = (!$this->hasReply()) ? $this->getOriginalFloors() : $this->getReplyFloors();
        } else {
            $floors = ($original) ? $this->getOriginalFloors() : $this->getReplyFloors();
        }

        return $this->calculateMaxHeight($floors);
    }

    public function isFullyOccupied(?bool $original = null): bool
    {
        //        if($this->isNew()){
        //            return $this->getLandArea() <= $this->getTotalArea($original);
        //        }else{
        //            return $this->getOccupiedArea() <= $this->getTotalArea($original);
        //        }

        //        if($this->isNew()){
        //            return true;
        //        }
        return $this->getTotalArea($original) >= $this->getMaxArea();
    }

    private function getFloorAmount(): int
    {
        $floors = (!$this->hasReply()) ? $this->getOriginalFloors() : $this->getReplyFloors();

        return $floors->count();
    }

    // just for original
    public function hasFloorAndIsNotCompletlyEmptyArea(): bool
    {
        return $this->hasOriginalFloors() && ($this->getUsefulArea() > 0);
    }

    public function getCus(?bool $original = null): float|int
    {
        return $this->getTotalArea($original) / $this->getLandArea();
    }

    public function getCos(?bool $original = null): string
    {
        return number_format((float) $this->getLand()?->getOccupiedArea() * 100 / $this->getLand()?->getLandArea(), 2);
    }

    public function canReply(): bool
    {
        if ($this->isNew()) {
            return false;
        }

        return !$this->notWallArea() && $this->hasFloorAndIsNotCompletlyEmptyArea() && $this->isFullyOccupied() && !$this->hasReply();
    }

    public function reply(EntityManagerInterface $entityManager, ?Building $parent = null): static
    {
        //        /** @var Floor $floor */
        //        foreach ($this->getOriginalFloors() as $floor) {
        //            $floor->reply($entityManager, $parent);
        //        }
        $this->replySons($entityManager, $this->getOriginalFloors(), $parent);

        $this->setHasReply(true);
        $entityManager->persist($this);

        $entityManager->flush();

        return $this;
    }

    public function allLocalsAreClassified(): bool
    {
        return $this->calculateAllLocalsAreClassified($this->getOriginalFloors());
    }

    /**
     * @return array<int>
     */
    public function getAmountTechnicalStatus(): array
    {
        $undefined = 0;
        $critical = 0;
        $bad = 0;
        $regular = 0;
        $good = 0;

        $floors = (!$this->hasReply()) ? $this->getOriginalFloors() : $this->getReplyFloors();

        foreach ($floors as $floor) {
            //            list($goodState, $regularState, $badState, $crititalState, $undefinedState) = $floor->getAmountTechnicalStatus(!$this->hasReply());
            $buildingAmountTechnicalStatus = $floor->getAmountTechnicalStatus();
            $goodState = $buildingAmountTechnicalStatus['good'];
            $regularState = $buildingAmountTechnicalStatus['regular'];
            $badState = $buildingAmountTechnicalStatus['bad'];
            $crititalState = $buildingAmountTechnicalStatus['critical'];
            $undefinedState = $buildingAmountTechnicalStatus['undefined'];

            $undefined += $undefinedState;
            $critical += $crititalState;
            $bad += $badState;
            $regular += $regularState;
            $good += $goodState;
        }

        return [
            'good' => $good,
            'regular' => $regular,
            'bad' => $bad,
            'critical' => $critical,
            'undefined' => $undefined,
        ];
    }

    /**
     * @return array<float>
     */
    public function getAmountMeterTechnicalStatus(): array
    {
        $undefined = 0;
        $critical = 0;
        $bad = 0;
        $regular = 0;
        $good = 0;

        $floors = (!$this->hasReply()) ? $this->getOriginalFloors() : $this->getReplyFloors();

        foreach ($floors as $floor) {
            $buildingAmountMeterTechnicalStatus = $floor->getAmountMeterTechnicalStatus();
            $goodState = $buildingAmountMeterTechnicalStatus['good'];
            $regularState = $buildingAmountMeterTechnicalStatus['regular'];
            $badState = $buildingAmountMeterTechnicalStatus['bad'];
            $crititalState = $buildingAmountMeterTechnicalStatus['critical'];
            $undefinedState = $buildingAmountMeterTechnicalStatus['undefined'];

            $undefined += $undefinedState;
            $critical += $crititalState;
            $bad += $badState;
            $regular += $regularState;
            $good += $goodState;
        }

        return [
            'good' => $good,
            'regular' => $regular,
            'bad' => $bad,
            'critical' => $critical,
            'undefined' => $undefined,
        ];
    }

    public function hasOriginalLocals(): bool
    {
        // TODO: esta correcto pero se debe poner de otra manera
        /** @var Floor $floor */
        foreach ($this->getOriginalFloors() as $floor) {
            if (!$floor->hasOriginalLocals()) {
                return false;
            }
        }

        return true;
    }

    public function getProjectCurrency(): ?string
    {
        return $this->getProject()?->getCurrency()?->getCode();
    }

    public function getTotalApprovedValueFormated(): string
    {
        return number_format((float) $this->getTotalApprovedValue() / 100, 2).' '.$this->getProjectCurrency();
    }

    public function getTotalEstimatedValueFormated(): string
    {
        return number_format((float) $this->getTotalEstimatedValue() / 100, 2).' '.$this->getProjectCurrency();
    }

    public function hasErrors(?bool $original = null): bool
    {
        if ($original) {
            foreach ($this->getOriginalFloors() as $floor) {
                if ($floor->hasErrors()) {
                    return true;
                }
            }
        } else {
            foreach ($this->getReplyFloors() as $floor) {
                if ($floor->hasErrors()) {
                    return true;
                }
            }
        }

        return $this->notWallArea() || (false == $this->hasOriginalLocals()) || (false == $this->allLocalsAreClassified()) || (false === $this->isFullyOccupied($original));
    }

    //    public function getUsefulArea(?bool $original = null): int
    //    {
    // //        $original = ($this instanceof Building) ? !$this->hasReply() : $this->isOriginal();
    //        return $this->getMeasurementData('getUsefulArea', $original);
    //    }

    public function getPopulation(): ?int
    {
        return $this->population;
    }

    public function setPopulation(?int $population): static
    {
        $this->population = $population;

        return $this;
    }

    public function getConstructionAssembly(): ?int
    {
        return $this->constructionAssembly;
    }

    public function setConstructionAssembly(?int $constructionAssembly): static
    {
        $this->constructionAssembly = $constructionAssembly;

        return $this;
    }

    public function getConstructionAssemblyComment(): ?string
    {
        return $this->constructionAssemblyComment;
    }

    public function setConstructionAssemblyComment(?string $constructionAssemblyComment): static
    {
        $this->constructionAssemblyComment = $constructionAssemblyComment;

        return $this;
    }

    /**
     * @return Collection<int, LandNetworkConnection>
     */
    public function getLandNetworkConnections(): Collection
    {
        return $this->landNetworkConnections;
    }

    public function addLandNetworkConnection(LandNetworkConnection $landNetworkConnection): static
    {
        if (!$this->landNetworkConnections->contains($landNetworkConnection)) {
            $this->landNetworkConnections->add($landNetworkConnection);
            $landNetworkConnection->setBuilding($this);
        }

        return $this;
    }

    public function removeLandNetworkConnection(LandNetworkConnection $landNetworkConnection): static
    {
        if ($this->landNetworkConnections->removeElement($landNetworkConnection)) {
            // set the owning side to null (unless already changed)
            if ($landNetworkConnection->getBuilding() === $this) {
                $landNetworkConnection->setBuilding(null);
            }
        }

        return $this;
    }

    public function hasReply(): ?bool
    {
        return $this->hasReply;
    }

    public function setHasReply(bool $hasReply): static
    {
        $this->hasReply = $hasReply;

        return $this;
    }

    /**
     * @param Collection<int, Floor> $items
     */
    private function replySons(EntityManagerInterface $entityManager, Collection $items, ?object $parent = null): void
    {
        foreach ($items as $item) {
            $item->reply($entityManager, $parent);
        }
    }

    public function getLocalsAmount(bool $reply = false): int
    {
        $locals = 0;

        $floors = ($this->hasReply() !== $reply) ? $this->getOriginalFloors() : $this->getReplyFloors();

        /** @var Floor $floor */
        foreach ($floors as $floor) {
            $locals += $floor->getLocalsAmount();
        }

        return $locals;
    }

    public function getAmountMeters(): ?float
    {
        $total = 0;
        $floors = (!$this->hasReply()) ? $this->getOriginalFloors() : $this->getReplyFloors();

        /** @var Floor $floor */
        foreach ($floors as $floor) {
            $total += $floor->getAmountMeters();
        }

        return $total;
    }

    public function getFloorsAmount(): int
    {
        $floors = (!$this->hasReply()) ? $this->getOriginalFloors() : $this->getReplyFloors();

        return $floors->count();
    }

    public function getSubsystemsAmount(): int
    {
        $subsystems = 0;
        $floors = (!$this->hasReply()) ? $this->getOriginalFloors() : $this->getReplyFloors();

        /** @var Floor $floor */
        foreach ($floors as $floor) {
            $subsystems += $floor->getSubSystemAmount();
        }

        return $subsystems;
    }

    /**
     * @return Collection<int, UrbanizationEstimate>
     */
    public function getUrbanizationEstimates(): Collection
    {
        return $this->urbanizationEstimates;
    }

    public function addUrbanizationEstimate(UrbanizationEstimate $urbanizationEstimate): static
    {
        if (!$this->urbanizationEstimates->contains($urbanizationEstimate)) {
            $this->urbanizationEstimates->add($urbanizationEstimate);
            $urbanizationEstimate->setBuilding($this);
        }

        return $this;
    }

    public function removeUrbanizationEstimate(UrbanizationEstimate $urbanizationEstimate): static
    {
        if ($this->urbanizationEstimates->removeElement($urbanizationEstimate)) {
            // set the owning side to null (unless already changed)
            if ($urbanizationEstimate->getBuilding() === $this) {
                $urbanizationEstimate->setBuilding(null);
            }
        }

        return $this;
    }

    public function getUrbanizationEstimateTotalPrice(): float
    {
        $price = 0;
        foreach ($this->urbanizationEstimates as $urbanizationEstimate) {
            $price += $urbanizationEstimate->getTotalPrice();
        }

        return $price;
    }

    /**
     * @return Collection<int, ProjectTechnicalPreparationEstimate>
     */
    public function getProjectTechnicalPreparationEstimates(): Collection
    {
        return $this->projectTechnicalPreparationEstimates;
    }

    public function addProjectTechnicalPreparationEstimate(ProjectTechnicalPreparationEstimate $projectTechnicalPreparationEstimate): static
    {
        if (!$this->projectTechnicalPreparationEstimates->contains($projectTechnicalPreparationEstimate)) {
            $this->projectTechnicalPreparationEstimates->add($projectTechnicalPreparationEstimate);
            $projectTechnicalPreparationEstimate->setBuilding($this);
        }

        return $this;
    }

    public function removeProjectTechnicalPreparationEstimate(ProjectTechnicalPreparationEstimate $projectTechnicalPreparationEstimate): static
    {
        if ($this->projectTechnicalPreparationEstimates->removeElement($projectTechnicalPreparationEstimate)) {
            // set the owning side to null (unless already changed)
            if ($projectTechnicalPreparationEstimate->getBuilding() === $this) {
                $projectTechnicalPreparationEstimate->setBuilding(null);
            }
        }

        return $this;
    }

    public function getProjectTechnicalPreparationEstimateTotalPrice(): float
    {
        $price = 0;
        foreach ($this->projectTechnicalPreparationEstimates as $projectTechnicalPreparationEstimate) {
            $price += $projectTechnicalPreparationEstimate->getTotalPrice();
        }

        return $price;
    }

    public function getPrice(?bool $original = null): int|float
    {
        if (0 === $this->getFloorsAmount()) {
            return 0;
        }

        $floors = (!$this->hasReply()) ? $this->getOriginalFloors() : $this->getReplyFloors();

        $price = 0;
        /** @var Floor $floor */
        foreach ($floors as $floor) {
            $price += $floor->getPrice();
        }

        return $price;
    }

    /**
     * @return Collection<int, BuildingSeparateConcept>
     */
    public function getBuildingSeparateConcepts(): Collection
    {
        return $this->buildingSeparateConcepts;
    }

    public function addBuildingSeparateConcept(BuildingSeparateConcept $buildingSeparateConcept): static
    {
        if (!$this->buildingSeparateConcepts->contains($buildingSeparateConcept)) {
            $this->buildingSeparateConcepts->add($buildingSeparateConcept);
            $buildingSeparateConcept->setBuilding($this);
        }

        return $this;
    }

    public function removeBuildingSeparateConcept(BuildingSeparateConcept $buildingSeparateConcept): static
    {
        if ($this->buildingSeparateConcepts->removeElement($buildingSeparateConcept)) {
            // set the owning side to null (unless already changed)
            if ($buildingSeparateConcept->getBuilding() === $this) {
                $buildingSeparateConcept->setBuilding(null);
            }
        }

        return $this;
    }

    public function getRangePrice(): int|float
    {
        return $this->getPrice() + $this->getUrbanizationEstimateTotalPrice() + $this->getProjectTechnicalPreparationEstimateTotalPrice();
    }

    public function getRangeMinPrice(): int|float
    {
        return $this->getRangePrice() - ($this->getRangePrice() * 20 / 100);
    }

    public function getRangeMaxPrice(): int|float
    {
        return $this->getRangePrice() + ($this->getRangePrice() * 20 / 100);
    }
}
