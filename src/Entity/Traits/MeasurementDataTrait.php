<?php

namespace App\Entity\Traits;

use App\Entity\Building;
use App\Entity\Floor;
use App\Entity\Interfaces\MeasurementDataInterface;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;

trait MeasurementDataTrait
{
    public function getTotalArea(bool $original = null): int
    {
        return $this->getUsefulArea($original) + $this->getWallArea($original) + $this->getEmptyArea($original);
    }

    public function getUsefulArea(bool $original = null): int
    {
//        $original = ($this instanceof Building) ? !$this->hasReply() : $this->isOriginal();
        return $this->getMeasurementData('getUsefulArea', $original);
    }

    public function getWallArea(bool $original = null): int
    {
        return $this->getMeasurementData('getWallArea', $original);
    }

    public function getEmptyArea(bool $original = null): int
    {
        return $this->getMeasurementData('getEmptyArea', $original);
    }

//    public function getUnassignedArea(bool $original = null): ?int
//    {
//        return $this->getMeasurementData('getUnassignedArea');
//    }

    private function calculateMaxHeight(Collection $items): float
    {
        $maxHeight = 0;
        /** @var MeasurementDataInterface $item */
        foreach ($items as $item){
            if($item->getMaxHeight() > $maxHeight){
                $maxHeight = $item->getMaxHeight();
            }
        }

        return $maxHeight;
    }

    public function calculateAllLocalsAreClassified(Collection $items): bool
    {
        if((!$this instanceof Building) && !$this->isOriginal()){
            return true;
        }

        if($items->count() == 0){
            return false;
        }

        foreach ($items as $item){
            if(!$item->allLocalsAreClassified()){
                return false;
            }
        }

        return true;
    }

    public function getVolume(bool $original = null): float|int
    {
        return $this->getTotalArea($original) * $this->getMaxHeight($original);
    }

    public function notWallArea(): bool
    {
        return $this->getWallArea() === 0;
    }

//    public function makeReply(EntityManagerInterface $entityManager, Collection $items, object $parent = null): static
//    {
//        $replica = clone $this;
//        $replica->setOriginal($this);
//
//        $entityManager->persist($replica);
//
//        foreach ($items as $item){
//            $item->reply($entityManager);
//        }
//
//        return $replica;
//    }
}
