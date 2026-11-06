<?php

declare(strict_types=1);

namespace App\Service\IteImport\Reader;

use App\Service\IteImport\DTO\IteImportRow;

/**
 * Reader para hojas con el formato "ITE CENCREM 2017 actualizado".
 *
 * Headers esperados:
 *   Calidad | Min | Max | Unidad de medida | Fuente | Año de referencia | Tipo de proyecto | Comentario
 */
final class InternationalDiruReader extends AbstractIteExcelReader
{
    protected const REQUIRED_HEADERS = [
        //        'calidad',
        //        'min',
        //        'max',
        //        'unidad de medida',
        //        'fuente',
        //        'año de referencia',
        'ciudad',
        'país',
        //        'tipo de proyecto',
        'acceso a fuente',
    ];

    protected function buildDto(array $row, string $sheetName, int $rowNumber): IteImportRow
    {
        return new IteImportRow(
            source: $this->requireString($row, 'fuente'),
            country: $this->requireString($row, 'país'),
            city: $this->requireString($row, 'ciudad'),
            projectType: $this->requireString($row, 'tipo de proyecto'),
            quality: $this->requireString($row, 'calidad'),
            measurementUnitCode: $this->requireString($row, 'unidad de medida'),
            min: $this->requireFloat($row, 'min'),
            max: $this->requireFloat($row, 'max'),
            yearReference: $this->requireInt($row, 'año de referencia'),
            sourceAccess: $this->optionalString($row, 'acceso a fuente'),
            comment: $this->optionalString($row, 'comentario'),
            originSheet: $sheetName,
            originRowNumber: $rowNumber,
        );
    }
}
