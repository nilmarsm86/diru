<?php

declare(strict_types=1);

namespace App\Service\IteImport\Reader;

use App\Service\IteImport\DTO\IteImportRow;

/**
 * Reader para hojas con el formato "ITE CENCREM 2017 actualizado".
 *
 * Headers esperados:
 *   source | country | city | description | unit
 *   ite_2017_usd | ite_2025_usd | quality | year_reference | methodology_note
 *
 * Particularidades del formato:
 *   - `description` mapea a `projectType` (es un tipo de obra granular: "Vivienda social", "Hospital", etc.).
 *   - `unit` es explícita por fila (USD/m2, USD/ml, USD/tn).
 *   - No hay min/max reales: usamos el valor 2025 actualizado como `min` y `max`.
 *     El valor histórico 2017 se conserva en el comentario para trazabilidad.
 */
final class CencremReader extends AbstractIteExcelReader
{
    protected const REQUIRED_HEADERS = [
        'source',
        'country',
        'city',
        'description',
        'unit',
        'ite_2017_usd',
        'ite_2025_usd',
        'quality',
        'year_reference',
    ];

    protected function buildDto(array $row, string $sheetName, int $rowNumber): IteImportRow
    {
        $methodology = $this->optionalString($row, 'methodology_note');
        $commentParts = [];

        if (null !== $methodology) {
            $commentParts[] = $methodology;
        }

        $comment = [] === $commentParts ? null : implode(' | ', $commentParts);

        return new IteImportRow(
            source: $this->requireString($row, 'source'),
            country: $this->requireString($row, 'country'),
            city: $this->requireString($row, 'city'),
            projectType: $this->requireString($row, 'description'),
            quality: $this->requireString($row, 'quality'),
            measurementUnitCode: $this->requireString($row, 'unit'),
            min: $this->requireFloat($row, 'ite_2017_usd'),
            max: $this->requireFloat($row, 'ite_2025_usd'),
            yearReference: $this->requireInt($row, 'year_reference'),
            sourceAccess: $this->optionalString($row, 'source_access'),
            comment: $comment,
            originSheet: $sheetName,
            originRowNumber: $rowNumber,
        );
    }
}
