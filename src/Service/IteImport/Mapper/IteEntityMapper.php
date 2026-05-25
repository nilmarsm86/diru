<?php

declare(strict_types=1);

namespace App\Service\IteImport\Mapper;

use App\Entity\Enums\IteQuality;
use App\Entity\Enums\IteType;
use App\Entity\Ite;
use App\Service\IteImport\DTO\IteImportRow;
use App\Service\IteImport\Repository\CityProvider;
use App\Service\IteImport\Repository\IteProjectTypeProvider;
use App\Service\IteImport\Repository\IteSourceProvider;
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
        private IteSourceProvider $iteSourceProvider,
        private CityProvider $cityProvider,
        private IteProjectTypeProvider $iteProjectTypeProvider,
    ) {
    }

    public function toEntity(IteImportRow $row): Ite
    {
        $unit = $this->measurementUnitProvider->getByCode($row->measurementUnitCode);
        $quality = IteQuality::getFromLabel($row->quality);
        $iteSource = $this->iteSourceProvider->getByName($row->source);
        $city = $this->cityProvider->getByName($row->city, $row->country);
        $iteProjectType = $this->iteProjectTypeProvider->getByName($row->projectType);

        return (new Ite())
            ->setQuality($quality)
            ->setMeasurementUnit($unit)
            ->setMin($row->min)
            ->setMax($row->max)
            ->setYearReference($row->yearReference)
            ->setComment($row->comment)
            ->setSourceAccess($row->sourceAccess)
            ->setSource($iteSource)
            ->setCity($city)
            ->setProjectType($iteProjectType)
            ->setType(('Cuba' === $row->country) ? IteType::National : IteType::International);
    }
}
