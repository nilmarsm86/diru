<?php

declare(strict_types=1);

namespace App\Service\IteImport\Mapper;

use App\Entity\Ite;
use App\Service\IteImport\DTO\IteImportRow;
use App\Service\IteImport\Repository\MeasurementUnitProvider;

/**
 * Convierte un {@see IteImportRow} en una entidad {@see Ite} lista para persistir.
 *
 * Se mantiene aislado del Reader (formato Excel) y del Orquestador (transacciones).
 * Si en el futuro la entidad cambia, este es el único punto que se toca.
 */
final readonly class IteEntityMapper
{
    public function __construct(
        private MeasurementUnitProvider $measurementUnitProvider,
    ) {
    }

    public function toEntity(IteImportRow $row): Ite
    {
        $unit = $this->measurementUnitProvider->getByCode($row->measurementUnitCode);

        return (new Ite())
            ->setSource($row->source)
            ->setCountry($row->country)
            ->setCity($row->city)
            ->setProjectType($row->projectType)
            ->setQuality($row->quality)
            ->setMeasurementUnit($unit)
            ->setMin($row->min)
            ->setMax($row->max)
            ->setYearReference($row->yearReference)
            ->setComment($row->comment);
    }
}
