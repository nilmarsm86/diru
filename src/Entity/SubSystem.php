<?php

namespace App\Entity;

use App\Entity\Enums\LocalTechnicalStatus;
use App\Entity\Enums\LocalType;
use App\Entity\Traits\NameToStringTrait;
use App\Repository\SubSystemRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;

#[ORM\Entity(repositoryClass: SubSystemRepository::class)]
#[DoctrineAssert\UniqueEntity(fields: ['name', 'floor'], message: 'Ya existe en la planta un subsistema con este nombre.', errorPath: 'name')]
class SubSystem
{
    use NameToStringTrait;

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
    #[Assert\Valid]
//    #[Assert\NotBlank(message: 'Establezca la planta para el subsistema.')]
    private ?Floor $floor = null;

    #[ORM\OneToOne(targetEntity: self::class, cascade: ['persist', 'remove'])]
    private ?self $original = null;

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

    public function getUsefulArea(): int
    {
        if($this->getLocalsAmount() === 0){
            return 0;
        }

        $usefulArea = 0;
        foreach ($this->locals as $local){
            if($local->getType() === LocalType::Local){
                $usefulArea += $local->getArea();
            }
        }

        return $usefulArea;
    }

    public function getWallArea(): int
    {
        if($this->getLocalsAmount() === 0){
            return 0;
        }

        $wallArea = 0;
        foreach ($this->locals as $local){
            if($local->getType() === LocalType::WallArea){
                $wallArea += $local->getArea();
            }
        }

        return $wallArea;
    }

    public function getEmptyArea(): int
    {
        if($this->getLocalsAmount() === 0){
            return 0;
        }

        $emptyArea = 0;
        foreach ($this->locals as $local){
            if($local->getType() === LocalType::EmptyArea){
                $emptyArea += $local->getArea();
            }
        }

        return $emptyArea;
    }

    public function getTotalSubSystemArea(): int
    {
        return $this->getUsefulArea() + $this->getWallArea() + $this->getEmptyArea();
    }

    public function getMaxHeight(): int
    {
        if($this->getLocalsAmount() === 0){
            return 0;
        }

        $maxHeight = 0;
        foreach ($this->locals as $local){
            if($local->getHeight() > $maxHeight){
                $maxHeight = $local->getHeight();
            }
        }

        return $maxHeight;
    }

    public function getVolume(): float|int
    {
        return $this->getTotalSubSystemArea() * $this->getMaxHeight();
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

    public function hasLocals(): bool
    {
        return $this->getLocalsAmount() > 0;
    }

    public function getLocalsAmount(): int
    {
        return $this->getLocals()->count();
    }

    public function allLocalsAreClassified(): bool
    {
        if($this->getLocalsAmount() == 0){
            return false;
        }

        foreach ($this->locals as $local){
            if(!$local->isClassified()){
                return false;
            }
        }

        return true;
    }

    public function getAmountLocalTechnicalStatus(): array
    {
        $undefined = 0;
        $critital = 0;
        $bad = 0;
        $regular = 0;
        $good = 0;

        foreach ($this->getLocals() as $local){
            $states = match ($local->getTechnicalStatus()) {
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

        foreach ($this->getLocals() as $local){
            $states = match ($local->getTechnicalStatus()) {
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
        foreach ($this->getLocals() as $local){
            $total += $local->getArea();
        }

        return $total;
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

    public function reply(): Floor|static
    {
        $replica = clone $this;
        $replica->setOriginal($this);

        return $replica;
    }
}
