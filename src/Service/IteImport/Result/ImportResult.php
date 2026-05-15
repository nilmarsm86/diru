<?php

declare(strict_types=1);

namespace App\Service\IteImport\Result;

/**
 * Resultado agregado de una importación: filas importadas, errores y hojas saltadas.
 *
 * Diseñado para ser mutado durante el proceso y consultado al final.
 */
final class ImportResult
{
    private int $importedCount = 0;
    private int $skippedRowCount = 0;

    /** @var list<ImportError> */
    private array $errors = [];

    /** @var list<string> */
    private array $skippedSheets = [];

    /** @var list<string> */
    private array $processedSheets = [];

    public function recordImported(): void
    {
        ++$this->importedCount;
    }

    public function recordSkippedRow(): void
    {
        ++$this->skippedRowCount;
    }

    public function recordError(ImportError $error): void
    {
        $this->errors[] = $error;
    }

    public function recordProcessedSheet(string $sheet): void
    {
        $this->processedSheets[] = $sheet;
    }

    public function recordSkippedSheet(string $sheet): void
    {
        $this->skippedSheets[] = $sheet;
    }

    public function getImportedCount(): int
    {
        return $this->importedCount;
    }

    public function getSkippedRowCount(): int
    {
        return $this->skippedRowCount;
    }

    /** @return list<ImportError> */
    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getErrorCount(): int
    {
        return count($this->errors);
    }

    /** @return list<string> */
    public function getSkippedSheets(): array
    {
        return $this->skippedSheets;
    }

    /** @return list<string> */
    public function getProcessedSheets(): array
    {
        return $this->processedSheets;
    }

    public function hasErrors(): bool
    {
        return [] !== $this->errors;
    }
}
