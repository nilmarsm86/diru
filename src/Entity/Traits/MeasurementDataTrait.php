<?php

namespace App\Entity\Traits;

use App\Entity\Floor;
use App\Entity\SubSystem;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

trait MeasurementDataTrait
{
    public function getTotalArea(?bool $original = null): float
    {
        return $this->getUsefulArea($original) + $this->getWallArea($original) + $this->getEmptyArea($original);
    }

    public function getUsefulArea(?bool $original = null): float
    {
        return $this->getMeasurementData('getUsefulArea', $original);
    }

    public function getWallArea(?bool $original = null): float
    {
        return $this->getMeasurementData('getWallArea', $original);
    }

    public function getEmptyArea(?bool $original = null): float
    {
        return $this->getMeasurementData('getEmptyArea', $original);
    }

    /**
     * @template T of SubSystem|Floor
     *
     * @param Collection<int, T> $items
     */
    private function calculateMaxHeight(Collection $items): float
    {
        $maxHeight = 0;
        foreach ($items as $item) {
            if ($item->getMaxHeight() > $maxHeight) {
                $maxHeight = $item->getMaxHeight();
            }
        }

        return $maxHeight;
    }

    /**
     * @template T of Floor|SubSystem
     *
     * @param ArrayCollection<int, T> $items
     */
    public function calculateAllLocalsAreClassified(ArrayCollection $items): bool
    {
        if (0 === $items->count()) {
            return false;
        }

        foreach ($items as $item) {
            if (!$item->allLocalsAreClassified()) {
                return false;
            }
        }

        return true;
    }

    public function getVolume(?bool $original = null): float|int
    {
        return $this->getTotalArea($original) * $this->getMaxHeight($original);
    }

    public function notWallArea(): bool
    {
        return 0.0 === $this->getWallArea();
    }
}
