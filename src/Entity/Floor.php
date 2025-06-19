<?php

namespace App\Entity;

use App\Entity\Enums\LocalType;
use App\Entity\Traits\NameToStringTrait;
use App\Repository\FloorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: FloorRepository::class)]
class Floor
{
    use NameToStringTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @var Collection<int, Local>
     */
    #[ORM\OneToMany(targetEntity: Local::class, mappedBy: 'floor')]
    #[Assert\Valid]
    private Collection $locals;

    #[ORM\ManyToOne(inversedBy: 'floors')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\Valid]
    #[Assert\NotBlank(message: 'Establezca la obra para la planta.')]
    private ?Building $building = null;

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
            $local->setFloor($this);
        }

        return $this;
    }

    public function removeLocal(Local $local): static
    {
        if ($this->locals->removeElement($local)) {
            // set the owning side to null (unless already changed)
            if ($local->getFloor() === $this) {
                $local->setFloor(null);
            }
        }

        return $this;
    }

    public function getUsefulArea(): int
    {
        if($this->locals->count() === 0){
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
        if($this->locals->count() === 0){
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
        if($this->locals->count() === 0){
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

    public function getTotalFloorArea(): int
    {
        return $this->getUsefulArea() + $this->getWallArea() + $this->getEmptyArea();
    }

    public function getMaxHeight(): int
    {
        if($this->locals->count() === 0){
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

    public function getBuilding(): ?Building
    {
        return $this->building;
    }

    public function setBuilding(?Building $building): static
    {
        $this->building = $building;

        return $this;
    }
}
