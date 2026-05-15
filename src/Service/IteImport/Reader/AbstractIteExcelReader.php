<?php

declare(strict_types=1);

namespace App\Service\IteImport\Reader;

use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * Comportamiento común a todos los Readers: extracción de headers, iteración
 * resiliente sobre filas, detección de fin de datos.
 *
 * Las subclases solo declaran:
 *   - REQUIRED_HEADERS  (qué columnas exige el formato)
 *   - buildDto()        (cómo construir el DTO a partir de la fila indexada por header)
 */
abstract class AbstractIteExcelReader implements IteExcelReaderInterface
{
    /** @var list<string> Headers obligatorios para que este Reader aplique. */
    protected const REQUIRED_HEADERS = [];

    public function supports(array $normalizedHeaders): bool
    {
        foreach (static::REQUIRED_HEADERS as $required) {
            if (!in_array($required, $normalizedHeaders, true)) {
                return false;
            }
        }

        return true;
    }

    public function read(Worksheet $worksheet): \Generator
    {
        $headers = $this->extractHeaders($worksheet);
        $sheetName = $worksheet->getTitle();
        $highestRow = $worksheet->getHighestDataRow();

        for ($rowNumber = 2; $rowNumber <= $highestRow; ++$rowNumber) {
            $values = $this->extractRowValues($worksheet, $rowNumber, $headers);

            if ($this->isEmptyRow($values)) {
                return; // Fin de datos en esta hoja.
            }

            try {
                $dto = $this->buildDto($values, $sheetName, $rowNumber);
                yield $rowNumber => RowReadResult::success($dto);
            } catch (\Throwable $e) {
                yield $rowNumber => RowReadResult::failure($e->getMessage(), $rowNumber, $sheetName);
            }
        }
    }

    /**
     * Construye el DTO normalizado desde un mapa header => valor.
     *
     * @param array<string, mixed> $row
     */
    abstract protected function buildDto(array $row, string $sheetName, int $rowNumber): \App\Service\IteImport\DTO\IteImportRow;

    /**
     * Devuelve los headers normalizados en orden de columna.
     *
     * @return list<string>
     */
    protected function extractHeaders(Worksheet $worksheet): array
    {
        $headers = [];
        $highestColumn = $worksheet->getHighestColumn();
        $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);

        for ($col = 1; $col <= $highestColumnIndex; ++$col) {
            /** @var string $value */
            $value = $worksheet->getCell([$col, 1])->getValue();
            $headers[] = $this->normalizeHeader($value ?? '');
        }

        return $headers;
    }

    /**
     * Construye un mapa header => valor para una fila concreta.
     *
     * @param list<string> $headers
     *
     * @return array<string, mixed>
     */
    protected function extractRowValues(Worksheet $worksheet, int $rowNumber, array $headers): array
    {
        $values = [];
        foreach ($headers as $index => $header) {
            if ('' === $header) {
                continue;
            }
            $cellValue = $worksheet->getCell([$index + 1, $rowNumber])->getValue();
            $values[$header] = is_string($cellValue) ? trim($cellValue) : $cellValue;
        }

        return $values;
    }

    /**
     * @param array<string, mixed> $values
     */
    protected function isEmptyRow(array $values): bool
    {
        foreach ($values as $v) {
            if (null !== $v && '' !== $v) {
                return false;
            }
        }

        return true;
    }

    protected function normalizeHeader(string $header): string
    {
        return strtolower(trim($header));
    }

    /**
     * Lee un valor obligatorio como string no vacío.
     *
     * @param array<string, mixed> $row
     */
    protected function requireString(array $row, string $key): string
    {
        if (!array_key_exists($key, $row) || null === $row[$key] || '' === $row[$key]) {
            throw new \InvalidArgumentException("Falta la columna obligatoria '{$key}'.");
        }

        /** @var string $value */
        $value = $row[$key];

        return trim($value);
    }

    /**
     * @param array<string, mixed> $row
     */
    protected function requireFloat(array $row, string $key): float
    {
        if (!array_key_exists($key, $row) || null === $row[$key] || '' === $row[$key]) {
            throw new \InvalidArgumentException("Falta la columna numérica obligatoria '{$key}'.");
        }
        if (!is_numeric($row[$key])) {
            /** @var string $rowKey */
            $rowKey = $row[$key];
            throw new \InvalidArgumentException("La columna '{$key}' debe ser numérica, recibido: '{$rowKey}'.");
        }

        return (float) $row[$key];
    }

    /**
     * @param array<string, mixed> $row
     */
    protected function requireInt(array $row, string $key): int
    {
        return (int) $this->requireFloat($row, $key);
    }

    /**
     * @param array<string, mixed> $row
     */
    protected function optionalString(array $row, string $key): ?string
    {
        if (!array_key_exists($key, $row) || null === $row[$key]) {
            return null;
        }
        /** @var string $value */
        $value = $row[$key];
        $value = trim($value);

        return '' === $value ? null : $value;
    }
}
