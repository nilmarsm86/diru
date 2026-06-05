<?php

namespace App\Service\Building;

use App\Entity\Building;
use App\Entity\BuildingSeparateConcept;

readonly class BuildingValuationService
{
    /**
     * Calcula el porcentaje total de conceptos separados (solo padres).
     * Regla de negocio: la suma de porcentajes de conceptos padre debe ser coherente.
     */
    public function getSeparateConceptsTotalPercent(Building $building): float
    {
        if ($building->getBuildingSeparateConcepts()->isEmpty()) {
            return 0.0;
        }

        return $building->getBuildingSeparateConcepts()
            ->filter(fn (BuildingSeparateConcept $concept) => $concept->isParent())
            ->reduce(fn (float $carry, BuildingSeparateConcept $concept) => $carry + (float) $concept->getPercentEstimatedAdjustValue(), 0.0);
    }

    /**
     * Resultado ITE = (Estimated Adjust Value) / 100 / Total Area
     * Devuelve 0 si no hay área (evita división por cero).
     */
    public function getResultIte(Building $building): float
    {
        $totalArea = $this->getTotalArea($building);
        if ($totalArea <= 0) {
            return 0.0;
        }

        $adjustValue = $building->getEstimatedAdjustValue();

        return $adjustValue / 100 / $totalArea;
    }

    //    /**
    //     * Valor estimado ajustado = RangePrice × Coefficient.
    //     */
    //    public function getEstimatedAdjustValue(Building $building): float
    //    {
    //        return $this->getRangePrice($building) * $building->getCoefficient();
    //    }

    //    /**
    //     * Precio base del rango (construcción + urbanización + preparación técnica).
    //     */
    //    public function getRangePrice(Building $building): float
    //    {
    //        return $this->getEstimatedConstructionAndNetworkConnection($building)
    //            + $this->getEstimatedUrbanizationAndNetworkConnection($building)
    //            + $building->getProjectTechnicalPreparationEstimateTotalPrice(); // si este sigue en Building, muévelo también
    //    }

    // ===================================================================
    // Cálculos que aún están en Building → los traemos aquí progresivamente
    // ===================================================================

    public function getEstimatedConstructionAndNetworkConnection(Building $building): float
    {
        //        $priceLandNetworkInside = $building->getLandNetworkConnections()
        //            ->filter(fn ($conn) => NetworkConnectionType::Inside === $conn->getType())
        //            ->reduce(fn (float $carry, $conn) => $carry + $conn->getTotalPrice(), 0.0);
        //
        //        return $this->getConstructivePrice($building) + $priceLandNetworkInside;
        return $building->getEstimatedConstructionAndNetworkConnection();
    }

    public function getEstimatedUrbanizationAndNetworkConnection(Building $building): float
    {
        //        $priceLandNetworkOutside = $building->getLandNetworkConnections()
        //            ->filter(fn ($conn) => NetworkConnectionType::Outside === $conn->getType())
        //            ->reduce(fn (float $carry, $conn) => $carry + $conn->getTotalPrice(), 0.0);
        //
        //        return $building->getUrbanizationEstimateTotalPrice() + $priceLandNetworkOutside;
        return $building->getEstimatedUrbanizationAndNetworkConnection();
    }

    //    private function getConstructivePrice(Building $building): float
    //    {
    //        if (0 === $building->getFloorsAmount()) {
    //            return 0.0;
    //        }
    //
    //        $floors = $building->hasReply()
    //            ? $building->getReplyFloors()
    //            : $building->getOriginalFloors();
    //
    //        return $floors->reduce(
    //            fn (float $carry, Floor $floor) => $carry + $floor->getPrice(),
    //            0.0
    //        );
    //    }

    private function getTotalArea(Building $building): float
    {
        // Aquí deberías decidir si usas original o reply según contexto.
        // Por ahora delegamos a la entidad, pero idealmente este cálculo también viene aquí.
        return $building->getTotalArea(); // temporal
    }

    public function getRangeMinPrice(Building $building): int|float
    {
        return $building->getRangePrice() - ($building->getRangePrice() * 30 / 100);
    }

    public function getRangeMaxPrice(Building $building): int|float
    {
        return $building->getRangePrice() + ($building->getRangePrice() * 30 / 100);
    }
}
