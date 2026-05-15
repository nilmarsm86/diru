<?php

declare(strict_types=1);

namespace App\Service\IteImport\Reader;

use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * Contrato de un Reader específico de un formato Excel concreto.
 *
 * Las implementaciones se autodetectan a partir de los headers de cada hoja
 * vía {@see IteReaderResolver}.
 */
interface IteExcelReaderInterface
{
    /**
     * Decide si esta implementación sabe leer una hoja con esos headers.
     *
     * @param list<string> $normalizedHeaders headers en minúsculas y sin espacios
     */
    public function supports(array $normalizedHeaders): bool;

    /**
     * Itera las filas de datos de la hoja, una a una, parando en la primera fila completamente vacía.
     *
     * @return \Generator<int, RowReadResult> clave: número de fila Excel (1-indexed)
     */
    public function read(Worksheet $worksheet): \Generator;
}
