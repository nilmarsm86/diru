<?php

namespace App\Entity;

use App\Entity\Interfaces\MoneyInterface;
use App\Repository\BuildingSeparateConceptRepository;
use App\Service\AssociativeEntryCollection;
use App\Service\FormulaEvaluator;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BuildingSeparateConceptRepository::class)]
class BuildingSeparateConcept implements MoneyInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'buildingSeparateConcepts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Building $building = null;

    #[ORM\ManyToOne(inversedBy: 'buildingSeparateConcepts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?SeparateConcept $separateConcept = null;

    #[ORM\Column]
    private ?float $percentEstimatedAdjustValue = null;

    #[ORM\Column]
    private ?float $percentEstimatedToExecuteValue = null;

    #[ORM\Column]
    private ?float $percentRealValue = null;

    public function __construct()
    {
        $this->percentEstimatedAdjustValue = 0;
        $this->percentEstimatedToExecuteValue = 0;
        $this->percentRealValue = 0;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBuilding(): ?Building
    {
        return $this->building;
    }

    public function setBuilding(?Building $building): static
    {
        $this->building = $building;

        return $this;
    }

    public function getSeparateConcept(): ?SeparateConcept
    {
        return $this->separateConcept;
    }

    public function setSeparateConcept(?SeparateConcept $separateConcept): static
    {
        $this->separateConcept = $separateConcept;

        return $this;
    }

    public function getPercentEstimatedAdjustValue(): ?float
    {
        return $this->percentEstimatedAdjustValue;
    }

    public function setPercentEstimatedAdjustValue(float $percentEstimatedAdjustValue): static
    {
        $this->percentEstimatedAdjustValue = $percentEstimatedAdjustValue;

        return $this;
    }

    public function getPercentEstimatedToExecuteValue(): ?float
    {
        return $this->percentEstimatedToExecuteValue;
    }

    public function setPercentEstimatedToExecuteValue(float $percentEstimatedToExecuteValue): static
    {
        $this->percentEstimatedToExecuteValue = $percentEstimatedToExecuteValue;

        return $this;
    }

    public function getPercentRealValue(): ?float
    {
        return $this->percentRealValue;
    }

    public function setPercentRealValue(float $percentRealValue): static
    {
        $this->percentRealValue = $percentRealValue;

        return $this;
    }

    public function getPrice(): float
    {
        return (float) $this->getBuilding()?->getConstructivePrice() * (float) $this->getPercentEstimatedAdjustValue() / 100;
    }

    public function getPriceEstimateToExecute(): float
    {
        return (float) $this->getBuilding()?->getConstructivePrice() * (float) $this->getPercentEstimatedToExecuteValue() / 100;
    }

    public function getPriceReal(): float
    {
        return (float) $this->getBuilding()?->getConstructivePrice() * (float) $this->getPercentRealValue() / 100;
    }

    public function getCurrency(): ?string
    {
        return $this->getBuilding()?->getProjectCurrency();
    }

    public function isParent(): bool
    {
        return null === $this->getSeparateConcept()?->getParent();
    }

    /**
     * @param array<string> $data
     */
    public function calculateImportByFormula(FormulaEvaluator $formulaEvaluator, array $data, string $formula): float
    {
        return (float) $formulaEvaluator->evaluar($data, $formula);
    }

    public function getImport(?float $parentImport = null, ?float $parentPercent = null): int|float
    {
        $import = (null === $parentImport) ? $this->getBuilding()?->getEstimatedAdjustValue() : $parentImport;
        $percent = (null === $parentPercent) ? $this->getPercentEstimatedAdjustValue() : $parentPercent;

        return round((float) $import * (float) $percent / 100);
    }

    public function getImportEstimateToExecuteValue(?float $parentImport = null, ?float $parentPercent = null): int|float
    {
        $import = (null === $parentImport) ? $this->getBuilding()?->getConstructionAssembly() : $parentImport;
        $percent = (null === $parentPercent) ? $this->getPercentEstimatedToExecuteValue() : $parentPercent;

        return round((float) $import * (float) $percent / 100);
    }

    public function getImportRealValue(?float $parentImport = null, ?float $parentPercent = null): int|float
    {
        $import = (null === $parentImport) ? $this->getBuilding()?->getConstructionRealValue() : $parentImport;
        $percent = (null === $parentPercent) ? $this->getPercentRealValue() : $parentPercent;

        return round((float) $import * (float) $percent / 100);
    }

    public function getPercentEstimatedAdjustValueByFormula(float $import): float
    {
        $value = $this->getBuilding()?->getEstimatedAdjustValue();

        if (null === $value || 0.0 === $value) {
            return 0;
        }

        return $import * 100 / $value;
    }

    public function getPercentEstimatedToExecuteValueByFormula(float $import): float
    {
        $value = (float) $this->getBuilding()?->getConstructionAssembly();

        if (0.0 === $value) {
            return 0;
        }

        return $import * 100 / $value;
    }

    public function getPercentRealValueByFormula(float $import): float
    {
        $value = (float) $this->getBuilding()?->getConstructionRealValue();

        if (0.0 === $value) {
            return 0;
        }

        return $import * 100 / $value;
    }

    /**
     * Extrae TODAS las claves numéricas (enteros o con puntos) de una fórmula.
     * Ejemplos que ahora reconoce correctamente:
     *   "4.1.1"  → clave "4.1.1"
     *   "10"     → clave "10"
     *   "2.5"    → clave "2.5"
     *   "-3.14"  → clave "-3.14" (si usas signos).
     *
     * Ignora operadores, paréntesis, espacios, etc.
     */
    /**
     * @param array <mixed> $ignoreNumbers
     *
     * @return array <mixed>
     */
    public function extraerNumeros(string $formula, array $ignoreNumbers): array
    {
        if ('' === trim($formula)) {
            return [];
        }

        // Patrón mejorado: secuencias de dígitos y puntos, pero que NO terminen ni empiecen con punto suelto,
        // y que permitan múltiples puntos (para claves tipo versión 4.1.1)
        //        $patron = '/\b(\d+(?:\.\d+)*)\b/';
        $patron = '/\b(\d+(?:\.\d+){0,3})\b/';

        preg_match_all($patron, $formula, $matches);

        // Limpiar y filtrar resultados vacíos o solo puntos
        $numeros = array_filter($matches[1], function ($item) {
            //            return '' !== $item && '.' !== $item && '' !== trim($item);
            $clean = trim($item);

            return '' !== $clean && '.' !== $clean;
        });

        // Convertir a string (ya que tus claves son strings) y eliminar duplicados
        $numeros = array_unique(array_map('strval', $numeros));

        $n = array_values($numeros); // reindexar
        $endNumbers = [];
        foreach ($n as $i) {
            if (!in_array($i, $ignoreNumbers, true)) {
                $endNumbers[] = $i;
            }
        }

        return $endNumbers;
    }

    //    /**
    //     * @param array<string> $numbers
    //     *
    //     * @return array<float>
    //     */
    //    public function conformData(AssociativeEntryCollection $aec, array $numbers): array
    //    {
    //        $data = [];
    //
    //        foreach ($numbers as $number) {
    //            $entry = $aec->find($number);
    //            if (null !== $entry) {
    //                $data[$number] = $entry->importAsFloat();
    //            }
    //        }
    //
    //        return $data;
    //    }
}
