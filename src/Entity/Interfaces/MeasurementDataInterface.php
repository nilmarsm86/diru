<?php

namespace App\Entity\Interfaces;

use Doctrine\ORM\EntityManagerInterface;

interface MeasurementDataInterface
{
    public function getMeasurementData(string $method, ?bool $original = null): float;

    public function getUnassignedArea(?bool $original = null): ?float;

    public function getFreeArea(?bool $original = null): ?float;

    public function getUsefulArea(?bool $original = null): float;

    public function getWallArea(?bool $original = null): float;

    public function getEmptyArea(?bool $original = null): float;

    //    public function getTotalArea(bool $original = true): float;
    public function getMaxHeight(?bool $original = null): float;

    public function isFullyOccupied(?bool $original = null): bool;

    //    public function reply(EntityManagerInterface $entityManager, object $parent = null): static;

    public function allLocalsAreClassified(): bool;

    /**
     * @return array<mixed>
     */
    public function getAmountTechnicalStatus(): array;

    /**
     * @return array<float>
     */
    public function getAmountMeterTechnicalStatus(): array;
}
