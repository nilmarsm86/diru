<?php

declare(strict_types=1);

namespace App\Service\IteImport\DTO;

/**
 * Representación normalizada de una fila Ite proveniente de cualquier formato Excel soportado.
 *
 * Actúa como tipo frontera entre los Readers (específicos por formato) y el Mapper
 * (específico por persistencia). Añadir un formato nuevo solo requiere implementar
 * un Reader que produzca este DTO.
 */
final readonly class IteImportRow
{
    public function __construct(
        public string $source,
        public string $country,
        public string $city,
        public string $projectType,
        public string $quality,
        public string $measurementUnitCode,
        public float $min,
        public float $max,
        public int $yearReference,
        public ?string $sourceAccess,
        public ?string $comment,
        public string $originSheet,
        public int $originRowNumber,
    ) {
    }
}
