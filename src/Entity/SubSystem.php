<?php

namespace App\Entity;

use App\Entity\Enums\LocalTechnicalStatus;
use App\Entity\Enums\LocalType;
use App\Entity\Interfaces\MeasurementDataInterface;
use App\Entity\Traits\MeasurementDataTrait;
use App\Entity\Traits\NameToStringTrait;
use App\Entity\Traits\OriginalTrait;
use App\Repository\SubSystemRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;

#[ORM\Entity(repositoryClass: SubSystemRepository::class)]
#[DoctrineAssert\UniqueEntity(fields: ['name', 'floor'], message: 'Ya existe en la planta un subsistema con este nombre.', errorPath: 'name',)]
class SubSystem implements MeasurementDataInterface
{
    use NameToStringTrait;
    use OriginalTrait;
    use MeasurementDataTrait;

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

    public function getOriginalLocals(): ArrayCollection
    {
        return $this->getItemsFilter($this->getLocals(), true);
    }

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
        return $this->getLocalsAmount(true) > 0;
    }

    public function hasReplyLocals(): bool
    {
        return $this->getLocalsAmount(false) > 0;
    }

    public function getMeasurementData(string $method, bool $original = true): mixed
    {
        $locals = ($original) ? $this->getOriginalLocals() : $this->getReplyLocals();

        $data = 0;
        foreach ($locals as $local) {
            $data += call_user_func([$locals, $method], $original);
        }

        return $data;
    }

    public function getUnassignedArea(bool $original = true): ?int
    {
        $isNew = $this->getFloor()->getBuilding()->isNew();
        $landArea = $this->getFloor()->getBuilding()->getLandArea();
        $occupiedArea = $this->getFloor()->getBuilding()->getOccupiedArea();
        return (($isNew) ? $landArea : $occupiedArea) - $this->getTotalArea($original);
    }

    public function getMaxHeight(bool $original = true): int
    {
        $locals = ($original) ? $this->getOriginalLocals() : $this->getReplyLocals();
        $maxHeight = 0;
        foreach ($locals as $local){
            if($local->getHeight() > $maxHeight){
                $maxHeight = $local->getHeight();
            }
        }

        return $maxHeight;
    }

    public function isFullyOccupied(bool $original = true): bool
    {
//        throw new \Exception("Not need");
//        if($this->getFloor()->getBuilding()->isNew()){
//            return true;
//        }

        return $this->getFloor()->isFullyOccupied($original);

    }

    public function getLocalsAmount(bool $original = true): int
    {
        $locals = ($original) ? $this->getOriginalLocals() : $this->getReplyLocals();
        return $locals->count();
    }

    public function reply(EntityManagerInterface $entityManager): static
    {
//        $replica = clone $this;
//        $replica->setOriginal($this);
//
//        $entityManager->persist($replica);
//
//        foreach ($this->getLocals() as $local) {
//            $local->reply($entityManager);
//        }
//
//        return $replica;
        return $this->makeReply($entityManager, $this->getLocals());
    }

    public function hasVariableHeights(bool $original = true): bool
    {
        $totalHeight = $this->getMeasurementData('getHeight', $original);
        return ($totalHeight % $this->getLocalsAmount($original)) > 0;
    }

    public function allLocalsAreClassified(): bool
    {
        //TODO: duda con esto
        if ($this->getLocalsAmount(true) == 0) {
            return true;
        }

        foreach ($this->getOriginalLocals() as $local) {
            if (!$local->isClassified()) {
                return false;
            }
        }

        return true;
    }

    public function getUsefulArea(bool $original = true): int
    {
        if ($this->getLocalsAmount($original) === 0) {
            return 0;
        }

        $locals = ($original) ? $this->getOriginalLocals() : $this->getReplyLocals();

        $usefulArea = 0;
        foreach ($locals as $local) {
            if ($local->getType() === LocalType::Local) {
                $usefulArea += $local->getArea();
            }
        }

        return $usefulArea;
    }

    public function getWallArea(bool $original = true): int
    {
        if ($this->getLocalsAmount($original) === 0) {
            return 0;
        }

        $locals = ($original) ? $this->getOriginalLocals() : $this->getReplyLocals();

        $wallArea = 0;
        foreach ($locals as $local) {
            if ($local->getType() === LocalType::WallArea) {
                $wallArea += $local->getArea();
            }
        }

        return $wallArea;
    }

    public function getEmptyArea(bool $original = true): int
    {
        if ($this->getLocalsAmount() === 0) {
            return 0;
        }

        $locals = ($original) ? $this->getOriginalLocals() : $this->getReplyLocals();

        $emptyArea = 0;
        foreach ($locals as $local) {
            if ($local->getType() === LocalType::EmptyArea) {
                $emptyArea += $local->getArea();
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

    public function getAmountLocalTechnicalStatus(bool $original = true): array
    {
        $undefined = 0;
        $critital = 0;
        $bad = 0;
        $regular = 0;
        $good = 0;

        $locals = ($original) ? $this->getOriginalLocals() : $this->getReplyLocals();

        foreach ($locals as $local) {
            match ($local->getTechnicalStatus()) {
                LocalTechnicalStatus::Critical => $critital++,
                LocalTechnicalStatus::Bad => $bad++,
                LocalTechnicalStatus::Regular => $regular++,
                LocalTechnicalStatus::Good => $good++,
                default => $undefined++
            };
        }

        return [
            'good' => $good,
            'regular' => $regular,
            'bad' => $bad,
            'critical' => $critital,
            'undefined' => $undefined
        ];
    }

    public function getAmountMeterTechnicalStatus(): array
    {
        $undefined = 0;
        $critital = 0;
        $bad = 0;
        $regular = 0;
        $good = 0;

        foreach ($this->getLocals() as $local) {
            match ($local->getTechnicalStatus()) {
                LocalTechnicalStatus::Critical => $critital += $local->getArea(),
                LocalTechnicalStatus::Bad => $bad += $local->getArea(),
                LocalTechnicalStatus::Regular => $regular += $local->getArea(),
                LocalTechnicalStatus::Good => $good += $local->getArea(),
                default => $undefined += $local->getArea()
            };
        }

        return [
            'good' => $good,
            'regular' => $regular,
            'bad' => $bad,
            'critical' => $critital,
            'undefined' => $undefined
        ];
    }

    public function getAmountMeters(): ?int
    {
        $total = 0;
        foreach ($this->getLocals() as $local) {
            $total += $local->getArea();
        }

        return $total;
    }

    public function getMaxLocalNumber(): int
    {
        $maxLocalNumber = 0;
        /** @var Local $local */
        foreach ($this->getOriginalLocals() as $local){
            if($local->getNumber() > $maxLocalNumber){
                $maxLocalNumber = $local->getNumber();
            }
        }

        return $maxLocalNumber;
    }

    public function createInitialLocal(): void
    {
        if(is_null($this->getId())){
            $local = Local::createAutomaticLocal($this, $this->getFloor()->getUnassignedArea() - 1, 1);
            $wall = Local::createAutomaticWall(1, 0);

            $this->addLocal($local);
            $this->addLocal($wall);
        }
    }

    public function inNewBuilding(): ?bool
    {
        return $this->getFloor()->inNewBuilding();
    }

    public function hasReply(): ?bool
    {
        return $this->getFloor()->hasReply();
    }

}
