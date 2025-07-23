<?php

namespace App\Entity;

use App\Entity\Enums\LocalType;
use App\Entity\Traits\NameToStringTrait;
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
class Floor
{
    use NameToStringTrait;

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

    #[ORM\OneToOne(targetEntity: self::class, cascade: ['persist', 'remove'])]
    private ?self $original = null;

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

    public function getOriginalSubsystems(): array
    {
        $originalSubsystems = [];
        foreach ($this->getSubSystems() as $subSystem){
            if($subSystem->isOriginal()){
                $originalSubsystems[] = $subSystem;
            }
        }

        return $originalSubsystems;
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

    public function getUsefulArea(): int
    {
        if($this->getOriginalSubSystemAmount() === 0){
            return 0;
        }

        $usefulArea = 0;
        foreach ($this->getOriginalSubsystems() as $subSystem){
            $usefulArea += $subSystem->getUsefulArea();
        }

        return $usefulArea;
    }

    public function getWallArea(): int
    {
        if($this->getOriginalSubSystemAmount() === 0){
            return 0;
        }

        $wallArea = 0;
        foreach ($this->getOriginalSubsystems() as $subSystem){
            $wallArea += $subSystem->getWallArea();
        }

        return $wallArea;
    }

    public function getEmptyArea(): int
    {
        if($this->getOriginalSubSystemAmount() === 0){
            return 0;
        }

        $emptyArea = 0;
        foreach ($this->getOriginalSubsystems() as $subSystem){
            $emptyArea += $subSystem->getEmptyArea();
        }

        return $emptyArea;
    }

    public function getTotalFloorArea(): int
    {
        return $this->getUsefulArea() + $this->getWallArea() + $this->getEmptyArea();
    }

    public function getMaxHeight(): int
    {
        if($this->getOriginalSubSystemAmount() === 0){
            return 0;
        }

        $maxHeight = 0;
        foreach ($this->getOriginalSubsystems() as $subSystem){
            if($subSystem->getMaxHeight() > $maxHeight){
                $maxHeight = $subSystem->getMaxHeight();
            }
        }

        return $maxHeight;
    }

    public function getVolume(): float|int
    {
        return $this->getTotalFloorArea() * $this->getMaxHeight();
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

    public function isFullyOccupied(): bool
    {
        if($this->getBuilding()->isNew()){
            return $this->getBuilding()->getLandArea() <= $this->getTotalFloorArea();
        }else{
            return $this->getBuilding()->getOccupiedArea() <= $this->getTotalFloorArea();
        }
    }

    public function getUnassignedArea(): ?int
    {
        if($this->getBuilding()->isNew()){
            return $this->getBuilding()->getLandArea() - $this->getTotalFloorArea();
        }else{
            return $this->getBuilding()->getOccupiedArea() - $this->getTotalFloorArea();
        }
    }

    public function hasSubSystemAndIsNotCompletlyEmptyArea(): bool
    {
        return $this->hasSubSystems() && ($this->getUsefulArea() > 0);
    }

    public function hasSubSystems(): bool
    {
        return $this->getOriginalSubSystemAmount() > 0;
    }

    public function getSubSystemAmount(): int
    {
        return $this->getSubSystems()->count();
    }

    public function getOriginalSubSystemAmount(): int
    {
        return count($this->getOriginalSubsystems());
    }

    public function getOriginal(): ?self
    {
        return $this->original;
    }

    public function setOriginal(?self $original): static
    {
        $this->original = $original;

        return $this;
    }

    public function reply(EntityManagerInterface $entityManager): Floor|static
    {
        $replica = clone $this;
        $replica->setOriginal($this);

        $entityManager->persist($replica);

        foreach ($this->getOriginalSubsystems() as $subSystem){
            $subSystem->reply($entityManager);
        }

        return $replica;
    }

    public function notWallArea(): bool
    {
        return $this->getWallArea() === 0;
    }

    public function getTotalArea(): int
    {
        return $this->getUsefulArea() + $this->getWallArea() + $this->getEmptyArea();
    }

    public function hasVariableHeights(): bool
    {
        /*$maxHeight = $this->getMaxHeight();
        foreach ($this->subSystems as $subSystem){
            if($subSystem->getMaxHeight() < $maxHeight){
                return true;
            }
        }*/

        $totalHeight = 0;
        foreach ($this->getOriginalSubsystems() as $subSystem){
            $totalHeight += $subSystem->getMaxHeight();
        }

        return ($totalHeight % $this->getOriginalSubSystemAmount()) > 0;
    }

    public function allLocalsAreClassified(): bool
    {
        if($this->getOriginalSubSystemAmount() == 0){
            return false;
        }

        foreach ($this->getOriginalSubsystems() as $subSystem){
            if(!$subSystem->allLocalsAreClassified()){
                return false;
            }
        }

        return true;
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
}
