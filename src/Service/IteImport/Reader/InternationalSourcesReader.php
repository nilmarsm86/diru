<?php

declare(strict_types=1);

namespace App\Service\IteImport\Reader;

use App\Service\IteImport\DTO\IteImportRow;

/**
 * Reader para hojas con el formato "BASE DE DATOS DE FUENTES INTERNACIONALES".
 *
 * Headers esperados (los dos últimos son opcionales según el año):
 *   source | country | city | project_type | quality_tier
 *   ite_min_usd_m2 | ite_max_usd_m2 | year_reference
 *   [methodology_note] | [source_access]
 *
 * La unidad de medida está implícita en el nombre de las columnas (USD/m²).
 */
final class InternationalSourcesReader extends AbstractIteExcelReader
{
    private const IMPLICIT_UNIT = 'm2';

    protected const REQUIRED_HEADERS = [
        'source',
        'country',
        'city',
        'project_type',
        'quality_tier',
        'ite_min_usd_m2',
        'ite_max_usd_m2',
        'year_reference',
    ];

    protected function buildDto(array $row, string $sheetName, int $rowNumber): IteImportRow
    {
        $methodology = $this->optionalString($row, 'methodology_note');
        //        $access = $this->optionalString($row, 'source_access');
        $comment = $this->joinNonEmpty([$methodology], ' | ');

        return new IteImportRow(
            source: $this->requireString($row, 'source'),
            country: $this->requireString($row, 'country'),
            city: $this->requireString($row, 'city'),
            projectType: $this->requireString($row, 'project_type'),
            quality: $this->requireString($row, 'quality_tier'),
            measurementUnitCode: self::IMPLICIT_UNIT,
            min: $this->requireFloat($row, 'ite_min_usd_m2'),
            max: $this->requireFloat($row, 'ite_max_usd_m2'),
            yearReference: $this->requireInt($row, 'year_reference'),
            sourceAccess: $this->optionalString($row, 'source_access'),
            comment: $comment,
            originSheet: $sheetName,
            originRowNumber: $rowNumber,
        );
    }

    /**
     * @param list<?string> $parts
     */
    private function joinNonEmpty(array $parts, string $glue): ?string
    {
        $filtered = array_filter($parts, static fn (?string $p): bool => null !== $p && '' !== $p);

        return [] === $filtered ? null : implode($glue, $filtered);
    }
}
