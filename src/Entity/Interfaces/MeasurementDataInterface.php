<?php

namespace App\Entity\Interfaces;

use App\Entity\Building;
use Doctrine\ORM\EntityManagerInterface;

interface MeasurementDataInterface
{
    public function getMeasurementData(string $method, bool $original = null): mixed;

    public function getUnassignedArea(bool $original = null): ?int;

    public function getUsefulArea(bool $original = null): int;

    public function getWallArea(bool $original = null): int;

    public function getEmptyArea(bool $original = null): int;

//    public function getTotalArea(bool $original = true): int;
    public function getMaxHeight(bool $original = null): int;

    public function isFullyOccupied(bool $original = null): bool;

//    public function reply(EntityManagerInterface $entityManager, object $parent = null): static;

    public function allLocalsAreClassified(): bool;

    public function getAmountLocalTechnicalStatus(): array;

    public function getAmountMeterTechnicalStatus(): array;

}
