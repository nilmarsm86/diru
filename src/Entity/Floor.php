<?php

namespace App\Entity;

use App\Entity\Interfaces\MeasurementDataInterface;
use App\Entity\Traits\HasReplyTrait;
use App\Entity\Traits\MeasurementDataTrait;
use App\Entity\Traits\NameToStringTrait;
use App\Entity\Traits\OriginalTrait;
use App\Entity\Traits\StructureStateTrait;
use App\Repository\FloorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;

#[ORM\Entity(repositoryClass: FloorRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[DoctrineAssert\UniqueEntity(fields: ['name', 'building', 'hasReply'], message: 'Ya existe en la obra una planta con este nombre.', errorPath: 'name')]
#[DoctrineAssert\UniqueEntity(fields: ['position', 'building', 'hasReply'], message: 'Ya existe en la obra una planta en esa posición.', errorPath: 'position')]
class Floor implements MeasurementDataInterface
{
    use NameToStringTrait;
    use MeasurementDataTrait;
    use StructureStateTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @var Collection<int, SubSystem>
     */
    #[ORM\OneToMany(targetEntity: SubSystem::class, mappedBy: 'floor', cascade: ['persist', 'remove'])]
    #[Assert\Valid]
    private Collection $subSystems;

    #[ORM\ManyToOne(inversedBy: 'floors')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\Valid]
//    #[Assert\NotBlank(message: 'Establezca la obra para la planta.')]
    private ?Building $building = null;

    #[ORM\Column]
    private ?bool $groundFloor = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'Establezca la posición de la planta.')]
    private ?int $position = null;

    public function __construct()
    {
        $this->subSystems = new ArrayCollection();
        $this->groundFloor = false;
        $this->position = 0;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, SubSystem>
     */
    public function getSubSystems(): Collection
    {
        return $this->subSystems;
    }

    public function getOriginalSubsystems(): ArrayCollection
    {
        return $this->getItemsFilter($this->getSubSystems(), true);
    }

    public function getReplySubsystems(): ArrayCollection
    {
        return $this->getItemsFilter($this->getSubSystems(), false);
    }

    public function addSubSystem(SubSystem $subSystem): static
    {
        if (!$this->subSystems->contains($subSystem)) {
            $this->subSystems->add($subSystem);
            $subSystem->setFloor($this);
        }

        return $this;
    }

    public function removeSubSystem(SubSystem $subSystem): static
    {
        if ($this->subSystems->removeElement($subSystem)) {
            // set the owning side to null (unless already changed)
            if ($subSystem->getFloor() === $this) {
                $subSystem->setFloor(null);
            }
        }

        return $this;
    }

    public function hasSubSystems(): bool
    {
        return $this->getSubSystemAmount() > 0;
    }

    public function hasOriginalSubSystems(): bool
    {
        return $this->getOriginalSubsystems()->count() > 0;
    }

    public function hasReplySubSystems(): bool
    {
        return $this->getReplySubsystems()->count() > 0;
    }

    public function getMeasurementData(string $method, bool $original = null): mixed
    {
        $subsystems = ($this->isOriginal()) ? $this->getOriginalSubsystems() : $this->getReplySubsystems();

        $data = 0;
        foreach ($subsystems as $subsystem) {
            $data += call_user_func([$subsystem, $method], $this->isOriginal());
        }

        return $data;
    }

    public function getUnassignedArea(bool $original = null): ?float
    {
//        if($this->getBuilding()->isNew()){
//            return $this->getBuilding()->getLandArea() - $this->getTotalArea();
//        }else{
//            return $this->getBuilding()->getOccupiedArea() - $this->getTotalArea();
//        }

        if($this->getBuilding()->getLand()->isBlocked()){
            return 0;
        }

        if (is_null($this->getBuilding())) {
            return 1;
        }

        $isNew = $this->getBuilding()->isNew();
        $landArea = $this->getBuilding()->getLandArea();
        $occupiedArea = $this->getBuilding()->getOccupiedArea();
        if($this->getTotalArea() > $occupiedArea){
            return $landArea - $this->getTotalArea();
        }else{
            return (($isNew) ? $landArea : $occupiedArea) - $this->getTotalArea();
        }
    }

    public function getFreeArea(bool $original = null): ?float
    {
        if(!$this->getBuilding()->getLand()->isBlocked()){
            return 0;
        }

        if (is_null($this->getBuilding())) {
            return 1;
        }

        $isNew = $this->getBuilding()->isNew();
        $landArea = $this->getBuilding()->getLandArea();
        $occupiedArea = $this->getBuilding()->getOccupiedArea();
//        return (($isNew) ? $landArea : $occupiedArea) - $this->getTotalArea();
        if($this->getTotalArea() > $occupiedArea){
            return $landArea - $this->getTotalArea();
        }else{
            return (($isNew) ? $landArea : $occupiedArea) - $this->getTotalArea();
        }
    }

    public function getMaxHeight(bool $original = null): float
    {
        $subSystems = ($this->isOriginal()) ? $this->getOriginalSubsystems() : $this->getReplySubsystems();
        return $this->calculateMaxHeight($subSystems);
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

    public function isGroundFloor(): ?bool
    {
        return $this->groundFloor;
    }

    public function setGroundFloor(bool $groundFloor): static
    {
        $this->groundFloor = $groundFloor;

        return $this;
    }

    public function isFullyOccupied(bool $original = null): bool
    {
//        if($this->getBuilding()->isNew()){
//            return $this->getBuilding()->getLandArea() <= $this->getTotalArea();
//        }else{
//            return $this->getBuilding()->getOccupiedArea() <= $this->getTotalArea();
//        }
//        if($this->getBuilding()->isNew()){
//            return true;
//        }

        return $this->getTotalArea() >= (($this->getBuilding()->isNew())
                ? $this->getBuilding()->getLandArea()
                : $this->getBuilding()->getOccupiedArea());
    }

    public function getSubSystemAmount(): int
    {
        $subsystems = ($this->isOriginal()) ? $this->getOriginalSubsystems() : $this->getReplySubsystems();
        return $subsystems->count();
    }

    public function hasSubSystemAndIsNotCompletlyEmptyArea(): bool
    {
        if($this->isOriginal()){
            return $this->hasOriginalSubSystems() && ($this->getUsefulArea() > 0);
        }else{
            return $this->hasReplySubSystems() && ($this->getUsefulArea() > 0);
        }
    }

    public function reply(EntityManagerInterface $entityManager, object $parent = null): static
    {
        $replica = clone $this;
        $replica->setOriginal($this);
        $replica->setName($replica->getName() . ' (R)');
        $replica->setHasReply(false);
        $replica->replica();

        $entityManager->persist($replica);

        $this->replySons($entityManager, $this->getOriginalSubsystems(), $replica);

        $this->setHasReply(true);
        $this->existingReplicated();
        $entityManager->persist($this);

        return $replica;
    }

    public function hasVariableHeights(): bool
    {
        $totalHeight = $this->getMeasurementData('getMaxHeight');
        return ($totalHeight % $this->getSubSystemAmount()) > 0;
    }

    public function allLocalsAreClassified(): bool
    {
        return $this->calculateAllLocalsAreClassified($this->getOriginalSubsystems());
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(int $position): static
    {
        $this->position = $position;

        return $this;
    }


    public function getAmountTechnicalStatus(): array
    {
        $undefined = 0;
        $critical = 0;
        $bad = 0;
        $regular = 0;
        $good = 0;

        $subsystems = ($this->isOriginal()) ? $this->getOriginalSubsystems() : $this->getReplySubsystems();

        foreach ($subsystems as $subsystem) {
            $subsystemAmountTechnicalStatus = $subsystem->getAmountTechnicalStatus($this->isOriginal());
            $goodState = $subsystemAmountTechnicalStatus['good'];
            $regularState = $subsystemAmountTechnicalStatus['regular'];
            $badState = $subsystemAmountTechnicalStatus['bad'];
            $crititalState = $subsystemAmountTechnicalStatus['critical'];
            $undefinedState = $subsystemAmountTechnicalStatus['undefined'];

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
            'undefined' => $undefined
        ];
    }

    public function getAmountMeterTechnicalStatus(): array
    {
        $undefined = 0;
        $critical = 0;
        $bad = 0;
        $regular = 0;
        $good = 0;

        $subsystems = ($this->isOriginal()) ? $this->getOriginalSubsystems() : $this->getReplySubsystems();

        foreach ($subsystems as $subsystem) {
            $subsystemAmountMeterTechnicalStatus = $subsystem->getAmountMeterTechnicalStatus($this->isOriginal());
            $goodState = $subsystemAmountMeterTechnicalStatus['good'];
            $regularState = $subsystemAmountMeterTechnicalStatus['regular'];
            $badState = $subsystemAmountMeterTechnicalStatus['bad'];
            $crititalState = $subsystemAmountMeterTechnicalStatus['critical'];
            $undefinedState = $subsystemAmountMeterTechnicalStatus['undefined'];

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
            'undefined' => $undefined
        ];
    }

    public function hasOriginalLocals(): bool
    {
        if ($this->getSubSystemAmount() === 0) {
            return false;
        }

        /** @var SubSystem $subSystem */
        foreach ($this->getOriginalSubsystems() as $subSystem) {
            if (!$subSystem->hasOriginalLocals()) {
                return false;
            }
        }

        return true;
    }

    public static function createAutomatic(?Floor $floor, Building $building, string $name, bool $isGroundFloor = false, int $position = 0, bool $reply = false, EntityManagerInterface $entityManager = null): static
    {
        if(is_null($floor)){
            $floor = new Floor();
            $floor->setPosition($position);
            $floor->setName($name);
            $floor->setGroundFloor($isGroundFloor);
        }
        $building->addFloor($floor);
        $floor->inNewBuilding() ? $floor->recent() : $floor->existingWithoutReplicating();
        if($reply){
            $floor->setHasReply(false);
            $floor->recent();
        }else{
            if($floor->inNewBuilding()){
                $floor->recent();
            }else{
                $floor->existingWithoutReplicating();
            }
        }
        $floor->createAutomaticSubsystem($reply, $entityManager);
        return $floor;
    }

    public function createAutomaticSubsystem(bool $reply = false, EntityManagerInterface $entityManager = null): void
    {
        if (is_null($this->getId())) {// TODO: no se pq hice esto
            SubSystem::createAutomatic(null, $this, 'Subsistema', $reply, $entityManager);
        }
    }

    public function inNewBuilding(): ?bool
    {
        if (is_null($this->getBuilding())) {
            return true;
        }
        return $this->getBuilding()->isNew();
    }

//    public function hasReply(): ?bool
//    {
//        if (!$this->inNewBuilding() && !$this->isOriginal()) {
//            return false;
//        }
//        return $this->getBuilding()->hasReply();
//    }

    public function hasErrors(): bool
    {
        return ($this->notWallArea() == true) || ($this->hasOriginalLocals() == false) || ($this->allLocalsAreClassified() == false) || ($this->isFullyOccupied() === false);
    }

    public function hasExtraSpace(): bool
    {
        if($this->getBuilding()->isNew()){
            return false;
        }
        return $this->getTotalArea() > $this->getBuilding()->getOccupiedArea();
    }

    public function getExtraSpace(): ?float
    {
        $extraSpace = $this->getTotalArea() - $this->getBuilding()->getOccupiedArea();
        if($extraSpace < 0){
            $extraSpace = 0;
        }

        return $extraSpace;
    }

    public function isNewInReply(): bool
    {
        return ($this->hasReply() === false) && (is_null($this->getOriginal()));
    }

    public function hasChangesFromOriginal(): bool
    {
        $subsystems = ($this->isOriginal()) ? $this->getOriginalSubsystems() : $this->getReplySubsystems();

        /** @var SubSystem $subsystem */
        foreach ($subsystems as $subsystem) {
            if ($subsystem->hasChangesFromOriginal()) {
                return true;
            }
        }

        return false;
    }

    public function hasBackgroundColorOfChange(): bool
    {
        return $this->isNewStructure() || $this->hasChangesFromOriginal();
    }

    public function hasRemoveConstructiveAction(): bool
    {
        $subsystems = ($this->isOriginal()) ? $this->getOriginalSubsystems() : $this->getReplySubsystems();

        /** @var SubSystem $subsystem */
        foreach ($subsystems as $subsystem) {
            if (!$subsystem->hasRemoveConstructiveAction()) {
                return false;
            }
        }

        return true;
    }

    public function getLocalsAmount(): int
    {
        $locals = 0;
        $subsystems = ($this->isOriginal()) ? $this->getOriginalSubsystems() : $this->getReplySubsystems();

        /** @var SubSystem $subsystem */
        foreach ($subsystems as $subsystem) {
            $locals += $subsystem->getLocalsAmount();
        }

        return $locals;
    }

    public function getAmountMeters(): ?float
    {
        $total = 0;
        $subsystems = ($this->isOriginal()) ? $this->getOriginalSubsystems() : $this->getReplySubsystems();
        /** @var SubSystem $subsystem */
        foreach ($subsystems as $subsystem) {
            $total += $subsystem->getAmountMeters();
        }

        return $total;
    }

    public function getPrice(bool $original = null): int
    {
        if ($this->getSubSystemAmount() === 0) {
            return 0;
        }

        $subSystems = ($this->isOriginal()) ? $this->getOriginalSubsystems() : $this->getReplySubsystems();

        $price = 0;
        /** @var SubSystem $subSystem */
        foreach ($subSystems as $subSystem) {
            $price += $subSystem->getPrice();
        }

        return $price;
    }

    public function getCurrency(): ?string
    {
        return $this->getBuilding()->getProjectCurrency();
    }

}
