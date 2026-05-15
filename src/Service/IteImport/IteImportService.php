<?php

declare(strict_types=1);

namespace App\Service\IteImport;

use App\Service\IteImport\DTO\IteImportRow;
use App\Service\IteImport\Exception\InvalidExcelFormatException;
use App\Service\IteImport\Mapper\IteEntityMapper;
use App\Service\IteImport\Reader\IteReaderResolver;
use App\Service\IteImport\Result\ImportError;
use App\Service\IteImport\Result\ImportResult;
use App\Service\IteImport\Validator\IteRowValidator;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Exception as PhpSpreadsheetReaderException;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * Orquestador del flujo completo de importación de un archivo Excel.
 *
 * Responsabilidades:
 *   1. Abrir el archivo de forma segura (lectura optimizada de PhpSpreadsheet).
 *   2. Validar que el archivo tenga al menos una hoja procesable.
 *   3. Para cada hoja: detectar formato → leer filas → validar → mapear → persistir.
 *   4. Acumular resultados/errores sin abortar ante fallos de fila individuales.
 *   5. Hacer flush por lotes para mantener controlado el consumo de memoria.
 *
 * No depende de ningún formato Excel concreto: añadir uno nuevo solo implica
 * registrar un nuevo Reader (lo descubre {@see IteReaderResolver} automáticamente).
 */
final class IteImportService
{
    private const DEFAULT_BATCH_SIZE = 200;

    public function __construct(
        private readonly IteReaderResolver $readerResolver,
        private readonly IteRowValidator $validator,
        private readonly IteEntityMapper $mapper,
        private readonly EntityManagerInterface $em,
        private readonly LoggerInterface $logger = new NullLogger(),
    ) {
    }

    /**
     * Importa todas las hojas con datos del archivo dado.
     *
     * @throws InvalidExcelFormatException si el archivo no puede abrirse o no tiene hojas reconocibles
     */
    public function import(string $filePath, int $batchSize = self::DEFAULT_BATCH_SIZE): ImportResult
    {
        $this->assertFileReadable($filePath);

        $spreadsheet = $this->openSpreadsheet($filePath);
        $result = new ImportResult();

        $anyReaderMatched = false;

        foreach ($spreadsheet->getWorksheetIterator() as $worksheet) {
            $sheetName = $worksheet->getTitle();

            if ($this->isWorksheetEmpty($worksheet)) {
                $result->recordSkippedSheet($sheetName);
                $this->logger->info('Saltando hoja vacía', ['sheet' => $sheetName]);
                continue;
            }

            $reader = $this->readerResolver->resolve($this->readHeaders($worksheet));
            if (null === $reader) {
                $result->recordSkippedSheet($sheetName);
                $this->logger->warning(
                    'Saltando hoja: ningún Reader reconoce sus headers',
                    ['sheet' => $sheetName],
                );
                continue;
            }

            $anyReaderMatched = true;
            $result->recordProcessedSheet($sheetName);
            $this->processSheet($worksheet, $reader, $result, $batchSize);
        }

        if (!$anyReaderMatched) {
            throw new InvalidExcelFormatException('Ninguna hoja del archivo tiene un formato reconocible. Revisa las cabeceras.');
        }

        $this->em->flush();

        return $result;
    }

    private function processSheet(
        Worksheet $worksheet,
        Reader\IteExcelReaderInterface $reader,
        ImportResult $result,
        int $batchSize,
    ): void {
        foreach ($reader->read($worksheet) as $rowReadResult) {
            if (!$rowReadResult->isSuccess()) {
                $result->recordError(new ImportError(
                    $rowReadResult->sheetName,
                    $rowReadResult->rowNumber,
                    $rowReadResult->error ?? 'Error desconocido',
                ));
                continue;
            }

            try {
                $row = $rowReadResult->row;
                assert($row instanceof IteImportRow);

                $this->validator->validate($row);
                $entity = $this->mapper->toEntity($row);
                $this->em->persist($entity);
                $result->recordImported();
            } catch (\Throwable $e) {
                $result->recordError(new ImportError(
                    $rowReadResult->sheetName,
                    $rowReadResult->rowNumber,
                    $e->getMessage(),
                ));
                continue;
            }

            if (0 === $result->getImportedCount() % $batchSize) {
                $this->em->flush();
            }
        }
    }

    private function assertFileReadable(string $filePath): void
    {
        if (!is_file($filePath) || !is_readable($filePath)) {
            throw new InvalidExcelFormatException("Archivo no accesible: {$filePath}");
        }
    }

    private function openSpreadsheet(string $filePath): Spreadsheet
    {
        try {
            $reader = IOFactory::createReaderForFile($filePath);
            $reader->setReadDataOnly(true);

            return $reader->load($filePath);
        } catch (PhpSpreadsheetReaderException $e) {
            throw new InvalidExcelFormatException("No se pudo leer el archivo Excel: {$e->getMessage()}", 0, $e);
        }
    }

    private function isWorksheetEmpty(Worksheet $worksheet): bool
    {
        // Una hoja vacía típicamente reporta dimensions A1:A1 con celda nula.
        $highestRow = $worksheet->getHighestDataRow();
        $highestColumn = $worksheet->getHighestDataColumn();

        if ($highestRow < 1 || '' === $highestColumn) {
            return true;
        }

        // Última heurística: si la celda A1 está vacía y solo hay una fila, está vacía.
        $a1 = $worksheet->getCell('A1')->getValue();

        return 1 === $highestRow && (null === $a1 || '' === $a1);
    }

    /**
     * @return list<string>
     */
    private function readHeaders(Worksheet $worksheet): array
    {
        $headers = [];
        $highestColumnIndex = Coordinate::columnIndexFromString($worksheet->getHighestColumn());

        for ($col = 1; $col <= $highestColumnIndex; ++$col) {
            /** @var string $value */
            $value = $worksheet->getCell([$col, 1])->getValue();
            $headers[] = strtolower(trim($value ?? ''));
        }

        return $headers;
    }
}
