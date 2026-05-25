<?php

declare(strict_types=1);

namespace App\Service\IteImport\Repository;

use App\Entity\MeasurementUnit;
use App\Repository\MeasurementUnitRepository;

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
    private const FIELD_NAME = 'code';

    /** @var array<string, MeasurementUnit> Cache por código normalizado. */
    private array $cache = [];

    public function __construct(
        private readonly MeasurementUnitRepository $measurementUnitRepository,
    ) {
    }

    // TODO: se puede aplicar el metodo plantilla
    /**
     * @throws \RuntimeException si la unidad no existe en BD
     */
    public function getByCode(string $code): MeasurementUnit
    {
        $normalized = $this->normalize($code);

        if (isset($this->cache[$normalized])) {
            return $this->cache[$normalized];
        }

        $unit = $this->measurementUnitRepository->findOneBy([self::FIELD_NAME => $normalized]);

        if (null === $unit) {
            throw new \RuntimeException(sprintf("MeasurementUnit con código '%s' (normalizado: '%s') no encontrada en BD.", $code, $normalized));
        }

        return $this->cache[$normalized] = $unit;
    }

    private function normalize(string $code): string
    {
        $code = trim($code);

        // "m²" → "m2", "M³" → "m3"
        return strtr($code, ['²' => '2', '³' => '3']);
    }
}
