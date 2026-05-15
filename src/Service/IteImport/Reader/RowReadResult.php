<?php

declare(strict_types=1);

namespace App\Service\IteImport\Reader;

use App\Service\IteImport\DTO\IteImportRow;

/**
 * Resultado de leer una fila del Excel.
 *
 * Permite que el Reader siga leyendo aunque una fila concreta sea malformada,
 * sin romper el generador con una excepción. El orquestador inspecciona cada
 * resultado y decide si persistir o registrar como error.
 */
final readonly class RowReadResult
{
    private function __construct(
        public ?IteImportRow $row,
        public ?string $error,
        public int $rowNumber,
        public string $sheetName,
    ) {
    }

    public static function success(IteImportRow $row): self
    {
        return new self($row, null, $row->originRowNumber, $row->originSheet);
    }

    public static function failure(string $error, int $rowNumber, string $sheetName): self
    {
        return new self(null, $error, $rowNumber, $sheetName);
    }

    public function isSuccess(): bool
    {
        return null !== $this->row;
    }
}
