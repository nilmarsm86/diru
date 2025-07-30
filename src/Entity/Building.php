<?php

namespace App\Entity;

use App\Entity\Enums\BuildingState;
use App\Entity\Interfaces\MeasurementDataInterface;
use App\Entity\Traits\MeasurementDataTrait;
use App\Entity\Traits\NameToStringTrait;
use App\Repository\BuildingRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;
use Exception;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;

#[ORM\Entity(repositoryClass: BuildingRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[DoctrineAssert\UniqueEntity(fields: ['name', 'project'], message: 'Ya existe en el proyecto una obra con este nombre.', errorPath: 'name')]
class Building implements MeasurementDataInterface
{
    use NameToStringTrait;
    use MeasurementDataTrait;

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

    #[ORM\Column(type: Types::BIGINT)]
    #[Assert\PositiveOrZero(message: 'El valor debe ser positivo')]
    private ?int $estimatedValueConstruction = 0;

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
     * @var Collection<int, DraftsmanBuilding>
     */
    #[ORM\OneToMany(targetEntity: ConstructorBuilding::class, mappedBy: 'building', cascade: ['persist'])]
    #[Assert\Valid()]
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
    private ?bool $hasReply = null;

    public function __construct()
    {
        $this->estimatedValueConstruction = 0;
        $this->estimatedValueEquipment = 0;
        $this->estimatedValueOther = 0;

        $this->approvedValueConstruction = 0;
        $this->approvedValueEquipment = 0;
        $this->approvedValueOther = 0;

        $this->setState(BuildingState::Registered);
        $this->draftsmansBuildings = new ArrayCollection();
        $this->constructorBuildings = new ArrayCollection();
        $this->floors = new ArrayCollection();

//        $this->isNew = false;

        $this->hasReply = false;
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
     * @throws Exception
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
        $this->state = "";
        $this->enumState = $enumState;

        return $this;
    }

    public function isStopped(): bool
    {
        return $this->getState() === BuildingState::Stopped;
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

    public function getEstimatedValueConstruction(): ?int
    {
        return $this->estimatedValueConstruction;
    }

    public function setEstimatedValueConstruction(?int $estimatedValueConstruction): static
    {
        $this->estimatedValueConstruction = $estimatedValueConstruction;

        return $this;
    }

    public function getEstimatedValueEquipment(): ?string
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

    public function getTotalEstimatedValue(): int|string|null
    {
        return $this->getEstimatedValueConstruction() + $this->getEstimatedValueEquipment() + $this->getEstimatedValueOther();
    }

    public function getTotalApprovedValue(): ?int
    {
        return $this->getApprovedValueConstruction() + $this->getApprovedValueEquipment() + $this->getApprovedValueOther();
    }

    //debo convertir el dinero en centavos, valores estimados y valores aprobados

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
                $actualDraftsmanBuilding->setFinishedAt(new \DateTimeImmutable());

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
                $actualConstrcutorBuilding->setFinishedAt(new \DateTimeImmutable());

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

    public function getOriginalFloors(): ArrayCollection
    {
        $originalFloors = new ArrayCollection();
        foreach ($this->getFloors() as $floor){
            if($floor->isOriginal()){
                $originalFloors->add($floor);
            }
        }

        return $originalFloors;
    }

    public function getReplyFloors(): ArrayCollection
    {
        $replyFloors = new ArrayCollection();
        foreach ($this->getFloors() as $floor){
            if(!$floor->isOriginal()){
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
        return $this->getFloorAmount(true) > 0;
    }

    public function hasReplyFloors(): bool
    {
        return $this->getFloorAmount(false) > 0;
    }

    public function isBuildingNew(): bool
    {
        return $this->hasOriginalFloors() === false;
    }

    public function cancel(): static
    {
        $this->setState(BuildingState::Canceled);

        return $this;
    }

    public function getLandArea(): ?int
    {
        return $this->getLand()->getLandArea();
    }

    public function getOccupiedArea(): ?int
    {
        return $this->getLand()->getOccupiedArea();
    }

    /*
     * Create the automatic the floors, based on land floors
     */
    public function createFloors(): static
    {
        $floor = $this->getLand()->getFloor();
        $this->createFloor('Planta Baja', true);

        if ($floor > 1) {
            for ($i = 1; $i < $floor; $i++) {
                $this->createFloor('Planta ' . $i, false, $i);
            }
        }

        return $this;
    }

    private function createFloor(string $name, bool $isGroundFloor=false, int $position=0): void
    {
        $f = new Floor();
        $f->setPosition($position);
        $f->setName($name);
        $f->setGroundFloor($isGroundFloor);
        $f->createAutomaticSubsystem();

        $this->addFloor($f);
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

    public function getMaxArea(): ?int
    {
        return ($this->isNew) ? $this->getLandArea() : $this->getOccupiedArea();
    }

    public function shortName(): ?string
    {
        if(strlen($this->getName()) > 50){
            return substr($this->getName(), 0, 50).'...';
        }
        return $this->getName();
    }

    public function getMeasurementData(string $method, bool $original = true): mixed
    {
        $floors = ($original) ? $this->getOriginalFloors() : $this->getReplyFloors();

        $data = 0;
        foreach ($floors as $floor){
            $data += call_user_func([$floor, $method], $original);
        }

        return $data;
    }

    public function getUnassignedArea(bool $original = true): ?int
    {
        return $this->getMeasurementData('getUnassignedArea', $original);
    }

    public function getMaxHeight(bool $original = true): int
    {
        $floors = ($original) ? $this->getOriginalFloors() : $this->getReplyFloors();
        return $this->calculateMaxHeight($floors, $original);
    }

    public function isFullyOccupied(bool $original = true): bool
    {
//        if($this->isNew()){
//            return $this->getLandArea() <= $this->getTotalArea($original);
//        }else{
//            return $this->getOccupiedArea() <= $this->getTotalArea($original);
//        }

        return $this->getTotalArea($original) >= (($this->isNew()) ? $this->getLandArea() : $this->getOccupiedArea());
    }

    private function getFloorAmount(bool $original = true): int
    {
        $floors = ($original) ? $this->getOriginalFloors() : $this->getReplyFloors();
        return $floors->count();
    }

    //just for original
    public function hasFloorAndIsNotCompletlyEmptyArea(): bool
    {
        return $this->hasOriginalFloors() && ($this->getUsefulArea(true) > 0);
    }

    public function getCus(bool $original = true): float|int
    {
        return $this->getTotalArea($original) / $this->getLandArea();
    }

    public function canReply(bool $original = true): bool
    {
        return (!$this->notWallArea($original) && $this->hasFloorAndIsNotCompletlyEmptyArea() && $this->isFullyOccupied($original) && !$this->hasReply());
    }

    public function reply(EntityManagerInterface $entityManager): static
    {
        foreach ($this->getOriginalFloors() as $floor){
            $floor->reply($entityManager);
        }

        $this->setHasReply(true);
        $entityManager->persist($this);

        $entityManager->flush();

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

    public function allLocalsAreClassified(): bool
    {
        return $this->calculateAllLocalsAreClassified($this->getOriginalFloors());
    }

    public function getAmountLocalTechnicalStatus(bool $original = true): array
    {
        $undefined = 0;
        $critital = 0;
        $bad = 0;
        $regular = 0;
        $good = 0;

        $floors = ($original) ? $this->getOriginalFloors() : $this->getReplyFloors();

        foreach ($floors as $floor) {
            list($goodState, $regularState, $badState, $crititalState, $undefinedState) = $floor->getAmountLocalTechnicalStatus($original);

            $undefined += $undefinedState;
            $critital += $crititalState;
            $bad += $badState;
            $regular += $regularState;
            $good += $goodState;
        }

        return [
            'good' => $good,
            'regular' => $regular,
            'bad' => $bad,
            'critical' => $critital,
            'undefined' => $undefined
        ];
    }

    public function getAmountMeterTechnicalStatus(bool $original = true): array
    {
        $undefined = 0;
        $critital = 0;
        $bad = 0;
        $regular = 0;
        $good = 0;

        $floors = ($original) ? $this->getOriginalFloors() : $this->getReplyFloors();

        foreach ($floors as $floor) {
            list($goodState, $regularState, $badState, $crititalState, $undefinedState) = $floor->getAmountMeterTechnicalStatus($original);

            $undefined += $undefinedState;
            $critital += $crititalState;
            $bad += $badState;
            $regular += $regularState;
            $good += $goodState;
        }

        return [
            'good' => $good,
            'regular' => $regular,
            'bad' => $bad,
            'critical' => $critital,
            'undefined' => $undefined
        ];
    }

    public function hasOriginalLocals(): bool
    {
        /** @var Floor $floor */
        foreach($this->getOriginalFloors() as $floor){
            if(!$floor->hasOriginalLocals()){
                return false;
            }
        }

        return true;
    }
}
