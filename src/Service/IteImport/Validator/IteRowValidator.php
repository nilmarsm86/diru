<?php

declare(strict_types=1);

namespace App\Service\IteImport\Validator;

use App\Entity\Enums\IteQuality;
use App\Entity\Ite;
use App\Service\IteImport\DTO\IteImportRow;
use App\Service\IteImport\Repository\CityProvider;
use App\Service\IteImport\Repository\IteProjectTypeProvider;
use App\Service\IteImport\Repository\IteSourceProvider;
use App\Service\IteImport\Repository\MeasurementUnitProvider;
use Doctrine\ORM\EntityManagerInterface;

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

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly MeasurementUnitProvider $measurementUnitProvider,
        private readonly IteSourceProvider $iteSourceProvider,
        private readonly CityProvider $cityProvider,
        private readonly IteProjectTypeProvider $iteProjectTypeProvider,
    ) {
    }

    /**
     * @throws \InvalidArgumentException si la fila viola alguna invariante
     * @throws \Exception
     */
    public function validate(IteImportRow $row): void
    {
        $this->assertPositive($row->min, 'min');
        $this->assertPositive($row->max, 'max');
        $this->assertRangeOrder($row->min, $row->max);
        $this->assertReasonableYear($row->yearReference);
        $this->assertExist($row);
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

    /**
     * @throws \Exception
     */
    private function assertExist(IteImportRow $row): void
    {
        $measurementUnit = $this->measurementUnitProvider->getByCode($row->measurementUnitCode);
        $source = $this->iteSourceProvider->getByName($row->source);
        $city = $this->cityProvider->getByName($row->city, $row->country);
        $iteProjectType = $this->iteProjectTypeProvider->getByName($row->projectType);

        $ite = $this->entityManager->getRepository(Ite::class)->findOneBy([
            'quality' => IteQuality::getFromLabel($row->quality),
            'min' => $row->min,
            'max' => $row->max,
            'measurementUnit' => $measurementUnit,
            'yearReference' => $row->yearReference,
            'source' => $source,
            'city' => $city,
            'projectType' => $iteProjectType,
        ]);

        if (null !== $ite) {
            throw new \Exception('El registro ITE ya existe.');
        }
    }
}
