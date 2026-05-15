<?php

declare(strict_types=1);

namespace App\Service\IteImport\Validator;

use App\Service\IteImport\DTO\IteImportRow;

/**
 * Valida invariantes de negocio sobre una fila ya parseada.
 *
 * Las validaciones estructurales (columnas faltantes, tipos incorrectos)
 * son responsabilidad del Reader. Aquí solo se validan reglas de dominio.
 */
final class IteRowValidator
{
    private const MIN_REASONABLE_YEAR = 1900;
    private const MAX_FUTURE_YEARS = 0;
    private const MIN_LAST_YEARS = 10;

    /**
     * @throws \InvalidArgumentException si la fila viola alguna invariante
     */
    public function validate(IteImportRow $row): void
    {
        $this->assertPositive($row->min, 'min');
        $this->assertPositive($row->max, 'max');
        $this->assertRangeOrder($row->min, $row->max);
        $this->assertReasonableYear($row->yearReference);
    }

    private function assertPositive(float $value, string $field): void
    {
        if ($value <= 0) {
            throw new \InvalidArgumentException(sprintf("El campo '%s' debe ser mayor que 0, recibido: %s.", $field, $value));
        }
    }

    private function assertRangeOrder(float $min, float $max): void
    {
        if ($min > $max) {
            throw new \InvalidArgumentException(sprintf("'min' (%s) no puede ser mayor que 'max' (%s).", $min, $max));
        }
    }

    private function assertReasonableYear(int $year): void
    {
        $currentYear = (int) date('Y');
        $maxAcceptable = $currentYear + self::MAX_FUTURE_YEARS;
        $minAcceptable = $currentYear - self::MIN_LAST_YEARS;

        if ($year < $minAcceptable || $year > $maxAcceptable) {
            throw new \InvalidArgumentException(sprintf('El año de referencia %d está fuera del rango razonable [%d-%d].', $year, self::MIN_REASONABLE_YEAR, $maxAcceptable));
        }
    }
}
