<?php

declare(strict_types=1);

namespace App\Service\IteImport\Repository;

use App\Entity\MeasurementUnit;
use App\Repository\MeasurementUnitRepository;
use App\Service\IteImport\Cache\LocalCacheTrait;

/**
 * Resuelve {@see MeasurementUnit} por código, con caché en memoria.
 *
 * El acceso por código se normaliza (mayúsculas/espacios/caracteres unicode
 * como "m²" → "m2") para que distintos formatos Excel apunten a la misma fila.
 *
 * IMPORTANTE: asume que la entidad MeasurementUnit tiene una propiedad
 * identificadora `code` (string). Si tu entidad usa otro nombre (ej. `symbol`),
 * ajusta {@see self::FIELD_NAME}.
 */
final class MeasurementUnitProvider
{
    use LocalCacheTrait;

    private const FIELD_NAME = 'code';

    public function __construct(
        private readonly MeasurementUnitRepository $measurementUnitRepository,
    ) {
    }

    public function getByCode(string $code): MeasurementUnit
    {
        $normalized = $this->normalize($code);

        /** @var MeasurementUnit $measurementUnit */
        $measurementUnit = $this->getCached($normalized, function () use ($normalized): MeasurementUnit {
            return $this->getOrCreate($normalized);
        });

        return $measurementUnit;
    }

    private function getOrCreate(string $normalized): MeasurementUnit
    {
        $unit = $this->measurementUnitRepository->findOneBy([self::FIELD_NAME => $normalized]);

        if (null === $unit) {
            $unit = new MeasurementUnit();
            $unit->setCode($normalized);
            $unit->setName($normalized);

            $this->measurementUnitRepository->save($unit);
        }

        return $unit;
    }

    private function normalize(string $code): string
    {
        $code = trim($code);

        // "m²" → "m2", "M³" → "m3"
        return strtr($code, ['²' => '2', '³' => '3']);
    }
}
