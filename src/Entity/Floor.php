<?php

namespace App\Entity;

use App\Entity\Enums\LocalTechnicalStatus;
use App\Entity\Interfaces\MeasurementDataInterface;
use App\Entity\Traits\MeasurementDataTrait;
use App\Entity\Traits\NameToStringTrait;
use App\Entity\Traits\OriginalTrait;
use App\Repository\FloorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;

#[ORM\Entity(repositoryClass: FloorRepository::class)]
#[DoctrineAssert\UniqueEntity(fields: ['name', 'building'], message: 'Ya existe en la obra una planta con este nombre.', errorPath: 'name')]
#[DoctrineAssert\UniqueEntity(fields: ['position', 'building'], message: 'Ya existe en la obra una planta en esa posición.', errorPath: 'position')]
class Floor implements MeasurementDataInterface
{
    use NameToStringTrait;
    use OriginalTrait;
    use MeasurementDataTrait;

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
    private ?int $position = null;

    public function __construct()
    {
        $this->subSystems = new ArrayCollection();
        $this->groundFloor = false;
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
        return $this->getSubSystemAmount(true) > 0;
    }

    public function hasReplySubSystems(): bool
    {
        return $this->getSubSystemAmount(false) > 0;
    }

    public function getMeasurementData(string $method, bool $original = true): mixed
    {
        $subsystems = ($original) ? $this->getOriginalSubsystems() : $this->getReplySubsystems();

        $data = 0;
        foreach ($subsystems as $subsystem){
            $data += call_user_func([$subsystem, $method], $original);
        }

        return $data;
    }

    public function getUnassignedArea(bool $original = true): ?int
    {
//        if($this->getBuilding()->isNew()){
//            return $this->getBuilding()->getLandArea() - $this->getTotalArea();
//        }else{
//            return $this->getBuilding()->getOccupiedArea() - $this->getTotalArea();
//        }

        if(is_null($this->getBuilding())){
            return 1;
        }

        $isNew = $this->getBuilding()->isNew();
        $landArea = $this->getBuilding()->getLandArea();
        $occupiedArea = $this->getBuilding()->getOccupiedArea();
        return (($isNew) ? $landArea : $occupiedArea) - $this->getTotalArea($original);
    }

    public function getMaxHeight(bool $original = true): int
    {
        $subSystems = ($original) ? $this->getOriginalSubsystems() : $this->getReplySubsystems();
        return $this->calculateMaxHeight($subSystems, $original);
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

    public function isFullyOccupied(bool $original = true): bool
    {
//        if($this->getBuilding()->isNew()){
//            return $this->getBuilding()->getLandArea() <= $this->getTotalArea();
//        }else{
//            return $this->getBuilding()->getOccupiedArea() <= $this->getTotalArea();
//        }
//        if($this->getBuilding()->isNew()){
//            return true;
//        }

        return $this->getTotalArea($original) >= (($this->getBuilding()->isNew()) ? $this->getBuilding()->getLandArea() : $this->getBuilding()->getOccupiedArea());
    }

    public function getSubSystemAmount(bool $original = true): int
    {
        $subsystems = ($original) ? $this->getOriginalSubsystems() : $this->getReplySubsystems();
        return $subsystems->count();
    }

    public function hasSubSystemAndIsNotCompletlyEmptyArea(): bool
    {
        return $this->hasOriginalSubSystems() && ($this->getUsefulArea(true) > 0);
    }

    public function reply(EntityManagerInterface $entityManager): static
    {
//        $replica = clone $this;
//        $replica->setOriginal($this);
//
//        $entityManager->persist($replica);
//
//        foreach ($this->getOriginalSubsystems() as $subSystem){
//            $subSystem->reply($entityManager);
//        }
//
//        return $replica;
        return $this->makeReply($entityManager, $this->getOriginalSubsystems());
    }

    public function hasVariableHeights(bool $original = true): bool
    {
        $totalHeight = $this->getMeasurementData('getMaxHeight', $original);
        return ($totalHeight % $this->getSubSystemAmount($original)) > 0;
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


    public function getAmountLocalTechnicalStatus(bool $original = true): array
    {
        $undefined = 0;
        $critital = 0;
        $bad = 0;
        $regular = 0;
        $good = 0;

        $subsystems = ($original) ? $this->getOriginalSubsystems() : $this->getReplySubsystems();

        foreach ($subsystems as $subsystem) {
            list($goodState, $regularState, $badState, $crititalState, $undefinedState) = $subsystem->getAmountLocalTechnicalStatus($original);

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

        $subsystems = ($original) ? $this->getOriginalSubsystems() : $this->getReplySubsystems();

        foreach ($subsystems as $subsystem) {
            list($goodState, $regularState, $badState, $crititalState, $undefinedState) = $subsystem->getAmountMeterTechnicalStatus($original);

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
        if($this->getSubSystemAmount() === 0){
            return false;
        }

        /** @var SubSystem $subSystem */
        foreach($this->getOriginalSubsystems() as $subSystem){
            if(!$subSystem->hasOriginalLocals()){
                return false;
            }
        }

        return true;
    }

    public function createAutomaticSubsystem(): void
    {
        if(is_null($this->getId())){
            $subSystem = new SubSystem();
            $subSystem->setFloor($this);
            $subSystem->setName('Subsistema');
            $subSystem->createInitialLocal();
            $this->addSubSystem($subSystem);
        }
    }

    public function inNewBuilding(): ?bool
    {
        if(is_null($this->getBuilding())){
            return true;
        }
        return $this->getBuilding()->isNew();
    }

    public function hasReply(): ?bool
    {
        return $this->getBuilding()->hasReply();
    }

    public function hasErrors(): bool
    {
        return ($this->notWallArea() == true) || ($this->hasOriginalLocals() == false) || ($this->allLocalsAreClassified() == false);
    }
}
