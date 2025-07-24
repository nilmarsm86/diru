<?php

namespace App\Entity\Traits;

use App\Entity\Floor;
use App\Entity\Interfaces\MeasurementDataInterface;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;

trait MeasurementDataTrait
{
    public function getTotalArea(bool $original = true): int
    {
        return $this->getUsefulArea($original) + $this->getWallArea($original) + $this->getEmptyArea($original);
    }

    public function getUsefulArea(bool $original = true): int
    {
        return $this->getMeasurementData('getUsefulArea', $original);
    }

    public function getWallArea(bool $original = true): int
    {
        return $this->getMeasurementData('getWallArea', $original);
    }

    public function getEmptyArea(bool $original = true): int
    {
        return $this->getMeasurementData('getEmptyArea', $original);
    }

    private function calculateMaxHeight(Collection $items, bool $original = true): int
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
        foreach ($items as $item){
            if(!$item->allLocalsAreClassified()){
                return false;
            }
        }

        return true;
    }

    public function getVolume(bool $original = true): float|int
    {
        return $this->getTotalArea($original) * $this->getMaxHeight($original);
    }

    public function notWallArea(bool $original = true): bool
    {
        return $this->getWallArea($original) === 0;
    }

    public function makeReply(EntityManagerInterface $entityManager, Collection $items): static
    {
        $replica = clone $this;
        $replica->setOriginal($this);

        $entityManager->persist($replica);

        foreach ($items as $item){
            $item->reply($entityManager);
        }

        return $replica;
    }
}