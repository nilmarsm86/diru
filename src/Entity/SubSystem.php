<?php

namespace App\Entity;

use App\Entity\Enums\TechnicalStatus;
use App\Entity\Interfaces\MeasurementDataInterface;
use App\Entity\Interfaces\MoneyInterface;
use App\Entity\Traits\MeasurementDataTrait;
use App\Entity\Traits\NameToStringTrait;
use App\Entity\Traits\StructureStateTrait;
use App\Repository\SubSystemRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SubSystemRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[DoctrineAssert\UniqueEntity(fields: ['name', 'floor'], message: 'Ya existe en la planta un subsistema con este nombre.', errorPath: 'name', )]
class SubSystem implements MeasurementDataInterface, MoneyInterface
{
    use NameToStringTrait;
    use MeasurementDataTrait;
    use StructureStateTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @var Collection<int, Local>
     */
    #[ORM\OneToMany(targetEntity: Local::class, mappedBy: 'subSystem', cascade: ['persist', 'remove'])]
    #[Assert\Valid]
    private Collection $locals;

    #[ORM\ManyToOne(inversedBy: 'subSystems')]
    #[ORM\JoinColumn(nullable: false)]
    //    #[Assert\Valid]
    //    #[Assert\NotBlank(message: 'Establezca la planta para el subsistema.')]
    private ?Floor $floor = null;

    #[ORM\ManyToOne(inversedBy: 'subSystems')]
    #[ORM\JoinColumn(nullable: true)]
    private ?SubsystemTypeSubsystemSubType $subsystemTypeSubsystemSubType = null;

    public function __construct()
    {
        $this->locals = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, Local>
     */
    public function getLocals(): Collection
    {
        return $this->locals;
    }

    /**
     * @return ArrayCollection<int, Local>
     */
    public function getOriginalLocals(): ArrayCollection
    {
        return $this->getItemsFilter($this->getLocals(), true);
    }

    /**
     * @return ArrayCollection<int, Local>
     */
    public function getReplyLocals(): ArrayCollection
    {
        return $this->getItemsFilter($this->getLocals(), false);
    }

    public function addLocal(Local $local): static
    {
        if (!$this->locals->contains($local)) {
            $this->locals->add($local);
            $local->setSubSystem($this);
        }

        return $this;
    }

    public function removeLocal(Local $local): static
    {
        if ($this->locals->removeElement($local)) {
            // set the owning side to null (unless already changed)
            if ($local->getSubSystem() === $this) {
                $local->setSubSystem(null);
            }
        }

        return $this;
    }

    public function hasLocals(): bool
    {
        return $this->getLocalsAmount() > 0;
    }

    public function hasOriginalLocals(): bool
    {
        return $this->getOriginalLocals()->count() > 0;
    }

    public function hasReplyLocals(): bool
    {
        return $this->getReplyLocals()->count() > 0;
    }

    public function getMeasurementData(string $method, ?bool $original = null): float
    {
        $locals = ($this->isOriginal()) ? $this->getOriginalLocals() : $this->getReplyLocals();

        $data = 0.0;
        foreach ($locals as $local) {
            $callback = [$local, $method];
            assert(is_callable($callback));
            /** @var int $callbackResult */
            $callbackResult = call_user_func($callback, $this->isOriginal());
            $data += $callbackResult;
        }

        return $data;
    }

    private function unassignedOrFreeArea(): float
    {
        if (is_null($this->getFloor()?->getBuilding())) {
            return 1;
        }

        $isNew = $this->getFloor()->getBuilding()->isNew();
        $landArea = (float) $this->getFloor()->getBuilding()->getLandArea();
        $occupiedArea = (float) $this->getFloor()->getBuilding()->getOccupiedArea();
        if ($this->getTotalArea() > $occupiedArea) {
            return $landArea - $this->getTotalArea();
        } else {
            return ((bool) $isNew ? $landArea : $occupiedArea) - $this->getTotalArea();
        }
    }

    public function getUnassignedArea(?bool $original = null): float
    {
        if (true === $this->getFloor()?->getBuilding()?->getLand()?->isBlocked()) {
            return 0;
        }

        //        if (is_null($this->getFloor()->getBuilding())) {
        //            return 1;
        //        }
        //
        //        $isNew = $this->getFloor()->getBuilding()->isNew();
        //        $landArea = $this->getFloor()->getBuilding()->getLandArea();
        //        $occupiedArea = $this->getFloor()->getBuilding()->getOccupiedArea();
        // //        return (($isNew) ? $landArea : $occupiedArea) - $this->getTotalArea();
        //        if($this->getTotalArea() > $occupiedArea){
        //            return $landArea - $this->getTotalArea();
        //        }else{
        //            return (($isNew) ? $landArea : $occupiedArea) - $this->getTotalArea();
        //        }
        return $this->unassignedOrFreeArea();
    }

    public function getFreeArea(?bool $original = null): float
    {
        if (false === $this->getFloor()?->getBuilding()?->getLand()?->isBlocked()) {
            return 0;
        }

        return $this->unassignedOrFreeArea();
    }

    public function getMaxHeight(?bool $original = null): float
    {
        $locals = ($this->isOriginal()) ? $this->getOriginalLocals() : $this->getReplyLocals();
        $maxHeight = 0;
        foreach ($locals as $local) {
            if ($local->getHeight() > $maxHeight) {
                $maxHeight = $local->getHeight();
            }
        }

        return $maxHeight;
    }

    public function isFullyOccupied(?bool $original = null): bool
    {
        //        throw new \Exception("Not need");
        //        if($this->getFloor()->getBuilding()->isNew()){
        //            return true;
        //        }

        if (is_null($this->getFloor())) {
            return false;
        }

        return $this->getFloor()->isFullyOccupied();
    }

    public function getLocalsAmount(): int
    {
        $locals = ($this->isOriginal()) ? $this->getOriginalLocals() : $this->getReplyLocals();

        return $locals->count();
    }

    public function getWallsAmount(): int
    {
        $walls = ($this->isOriginal()) ? $this->getOriginalLocals() : $this->getReplyLocals();
        $wallsAmount = 0;
        /** @var Local $wall */
        foreach ($walls as $wall) {
            if ($wall->isWallType() && !is_null($wall->getId())) {
                ++$wallsAmount;
            }
        }

        return $wallsAmount;
    }

    public function hasWalls(): bool
    {
        return $this->getWallsAmount() > 0;
    }

    public function reply(EntityManagerInterface $entityManager, ?Floor $parent = null): static
    {
        $replica = clone $this;
        $replica->setOriginal($this);
        $replica->setName($replica->getName().' (R)');
        $replica->setFloor($parent);
        $replica->setHasReply(false);
        $replica->replica();

        $entityManager->persist($replica);

        $this->replySons($entityManager, $this->getOriginalLocals(), $replica);

        $this->setHasReply(true);
        $this->existingReplicated();
        $entityManager->persist($this);

        return $replica;
    }

    public function hasVariableHeights(): bool
    {
        $totalHeight = $this->getMeasurementData('getHeight');

        return ($totalHeight % $this->getLocalsAmount()) > 0;
    }

    public function allLocalsAreClassified(): bool
    {
        // TODO: duda con esto
        if (0 === $this->getLocalsAmount()) {
            return true;
        }

        $locals = ($this->isOriginal()) ? $this->getOriginalLocals() : $this->getReplyLocals();
        foreach ($locals as $local) {
            if (!$local->isClassified()) {
                return false;
            }
        }

        return true;
    }

    public function getUsefulArea(?bool $original = null): float
    {
        if (0 === $this->getLocalsAmount()) {
            return 0;
        }

        $locals = ($this->isOriginal()) ? $this->getOriginalLocals() : $this->getReplyLocals();

        $usefulArea = 0;
        /** @var Local $local */
        foreach ($locals as $local) {
            if ($local->isLocalType()) {
                $usefulArea += (float) $local->getArea();
            }
        }

        return $usefulArea;
    }

    public function getWallArea(?bool $original = null): float
    {
        if (0 === $this->getLocalsAmount()) {
            return 0;
        }

        $locals = ($this->isOriginal()) ? $this->getOriginalLocals() : $this->getReplyLocals();

        $wallArea = 0;
        /** @var Local $local */
        foreach ($locals as $local) {
            if ($local->isWallType()) {
                $wallArea += (float) $local->getArea();
            }
        }

        return $wallArea;
    }

    public function getEmptyArea(?bool $original = null): float
    {
        if (0 === $this->getLocalsAmount()) {
            return 0;
        }

        $locals = ($this->isOriginal()) ? $this->getOriginalLocals() : $this->getReplyLocals();

        $emptyArea = 0;
        /** @var Local $local */
        foreach ($locals as $local) {
            if ($local->isEmptyType()) {
                $emptyArea += (float) $local->getArea();
            }
        }

        return $emptyArea;
    }

    public function getFloor(): ?Floor
    {
        return $this->floor;
    }

    public function setFloor(?Floor $floor): static
    {
        $this->floor = $floor;

        return $this;
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

        $locals = ($this->isOriginal()) ? $this->getOriginalLocals() : $this->getReplyLocals();

        foreach ($locals as $local) {
            match ($local->getTechnicalStatus()) {
                TechnicalStatus::Critical => $critical++,
                TechnicalStatus::Bad => $bad++,
                TechnicalStatus::Regular => $regular++,
                TechnicalStatus::Good => $good++,
                default => $undefined++,
            };
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

        foreach ($this->getLocals() as $local) {
            match ($local->getTechnicalStatus()) {
                TechnicalStatus::Critical => $critical += (float) $local->getArea(),
                TechnicalStatus::Bad => $bad += (float) $local->getArea(),
                TechnicalStatus::Regular => $regular += (float) $local->getArea(),
                TechnicalStatus::Good => $good += (float) $local->getArea(),
                default => $undefined += (float) $local->getArea(),
            };
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
     * @return array<mixed>
     */
    public function getAmountConstructiveAction(): array
    {
        $locals = ($this->isOriginal()) ? $this->getOriginalLocals() : $this->getReplyLocals();
        $constructiveAction = [];

        /** @var Local $local */
        foreach ($locals as $local) {
            $key = $local->getLocalConstructiveAction()?->getConstructiveAction()?->getName();
            if (!is_null($key)) {
                $constructiveAction[$key] = array_key_exists($key, $constructiveAction) ? $constructiveAction[$key] + 1 : 1;
            }
        }

        return $constructiveAction;
    }

    /**
     * @return array<mixed>
     */
    public function getPriceByConstructiveAction(): array
    {
        $locals = ($this->isOriginal()) ? $this->getOriginalLocals() : $this->getReplyLocals();
        $constructiveAction = [];

        /** @var Local $local */
        foreach ($locals as $local) {
            $key = $local->getLocalConstructiveAction()?->getConstructiveAction()?->getName();
            if (!is_null($key)) {
                $constructiveAction[$key] = array_key_exists($key, $constructiveAction) ? $constructiveAction[$key] + $local->getConstructiveActionAmount() : $local->getConstructiveActionAmount();
            }
        }

        return $constructiveAction;
    }

    /**
     * @return array<mixed>
     */
    public function getMeterByConstructiveAction(): array
    {
        $locals = ($this->isOriginal()) ? $this->getOriginalLocals() : $this->getReplyLocals();
        $constructiveAction = [];

        /** @var Local $local */
        foreach ($locals as $local) {
            $key = $local->getLocalConstructiveAction()?->getConstructiveAction()?->getName();
            if (!is_null($key)) {
                $constructiveAction[$key] = array_key_exists($key, $constructiveAction) ? $constructiveAction[$key] + (float) $local->getArea() : (float) $local->getArea();
            }
        }

        return $constructiveAction;
    }

    public function getAmountMeters(): ?float
    {
        $total = 0;
        $locals = ($this->isOriginal()) ? $this->getOriginalLocals() : $this->getReplyLocals();
        /** @var Local $local */
        foreach ($locals as $local) {
            $total += (float) $local->getArea();
        }

        return $total;
    }

    public function getMaxLocalNumber(): int|string
    {
        $locals = ($this->isOriginal()) ? $this->getOriginalLocals() : $this->getReplyLocals();

        $maxLocalNumber = 0;
        /** @var Local $local */
        foreach ($locals as $local) {
            if ($local->getNumber() > $maxLocalNumber) {
                $maxLocalNumber = $local->getNumber();
            }
        }

        return $maxLocalNumber;
    }

    public function createInitialLocal(bool $reply = false, ?EntityManagerInterface $entityManager = null): void
    {
        if (is_null($this->getId())) {
            $unassignedArea = (float) $this->getFloor()?->getUnassignedArea() - 1;
            Local::createAutomaticLocal(null, $this, $unassignedArea, 1, $reply, $entityManager);
            Local::createAutomaticWall($this, 1, 0, $reply, $entityManager);
        }
    }

    public static function createAutomatic(?SubSystem $subSystem, Floor $floor, string $name, bool $reply = false, ?EntityManagerInterface $entityManager = null): self
    {
        if (is_null($subSystem)) {
            $subSystem = new SubSystem();
            $subSystem->setName($name);
        }
        $floor->addSubSystem($subSystem);

        //        $subSystem->setFloor($floor);

        (true === $floor->inNewBuilding()) ? $subSystem->recent() : $subSystem->existingWithoutReplicating();
        if ($reply) {
            $subSystem->setHasReply(false);
            $subSystem->recent();
        } else {
            //            if ($floor->inNewBuilding()) {
            //                $subSystem->recent();
            //            } else {
            //                $subSystem->existingWithoutReplicating();
            //            }
            (true === $floor->inNewBuilding()) ? $subSystem->recent() : $subSystem->existingWithoutReplicating();
        }
        $subSystem->createInitialLocal($reply, $entityManager);

        return $subSystem;
    }

    public function inNewBuilding(): ?bool
    {
        return $this->getFloor()?->inNewBuilding();
    }

    public function hasReply(): ?bool
    {
        if (false === $this->inNewBuilding() && false === $this->isOriginal()) {
            return false;
        }

        return $this->getFloor()?->hasReply();
    }

    public function hasErrors(): bool
    {
        return (false === $this->allLocalsAreClassified()) || $this->notWallArea() || (false === $this->isFullyOccupied());
    }

    public function isNewInReply(): bool
    {
        return (false === $this->hasReply()) && is_null($this->getOriginal());
    }

    public function hasChangesFromOriginal(): bool
    {
        $locals = ($this->isOriginal()) ? $this->getOriginalLocals() : $this->getReplyLocals();

        /** @var Local $local */
        foreach ($locals as $local) {
            if ($local->hasChangesFromOriginal()) {
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
        $locals = ($this->isOriginal()) ? $this->getOriginalLocals() : $this->getReplyLocals();

        /** @var Local $local */
        foreach ($locals as $local) {
            if (!$local->hasRemoveConstructiveAction()) {
                return false;
            }
        }

        return true;
    }

    public function getPrice(?bool $original = null): int|float
    {
        if (0 === $this->getLocalsAmount()) {
            return 0;
        }

        $locals = ($this->isOriginal()) ? $this->getOriginalLocals() : $this->getReplyLocals();

        $price = 0;
        /** @var Local $local */
        foreach ($locals as $local) {
            $price += $local->getConstructiveActionAmount();
        }

        return $price;
    }

    public function getCurrency(): ?string
    {
        $floor = $this->getFloor();
        $building = $floor?->getBuilding();

        return $building?->getProjectCurrency();
    }

    public function getSubsystemTypeSubsystemSubType(): ?SubsystemTypeSubsystemSubType
    {
        return $this->subsystemTypeSubsystemSubType;
    }

    public function setSubsystemTypeSubsystemSubType(?SubsystemTypeSubsystemSubType $subsystemTypeSubsystemSubType): static
    {
        $this->subsystemTypeSubsystemSubType = $subsystemTypeSubsystemSubType;

        return $this;
    }
}
