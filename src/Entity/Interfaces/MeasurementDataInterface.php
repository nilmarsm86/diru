<?php

namespace App\Entity\Interfaces;

use App\Entity\Building;
use Doctrine\ORM\EntityManagerInterface;

interface MeasurementDataInterface
{
    public function getMeasurementData(string $method, bool $original = true): mixed;

    public function getUnassignedArea(bool $original = true): ?int;

    public function getUsefulArea(bool $original = true): int;

    public function getWallArea(bool $original = true): int;

    public function getEmptyArea(bool $original = true): int;

//    public function getTotalArea(bool $original = true): int;
    public function getMaxHeight(bool $original = true): int;

    public function isFullyOccupied(bool $original = true): bool;

    public function reply(EntityManagerInterface $entityManager): static;

    public function allLocalsAreClassified(): bool;

    public function getAmountLocalTechnicalStatus(): array;

    public function getAmountMeterTechnicalStatus(): array;

}
