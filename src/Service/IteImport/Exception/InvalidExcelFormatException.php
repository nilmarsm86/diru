<?php

declare(strict_types=1);

namespace App\Service\IteImport\Exception;

/**
 * Se lanza cuando el archivo Excel no puede abrirse o tiene una estructura
 * fundamentalmente inválida (ej. ninguna hoja con datos reconocibles).
 *
 * Distinta de errores por fila: esta excepción aborta la importación entera.
 */
final class InvalidExcelFormatException extends \RuntimeException
{
}
