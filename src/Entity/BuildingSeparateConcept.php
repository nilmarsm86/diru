<?php

namespace App\Entity;

use App\Entity\Interfaces\MoneyInterface;
use App\Repository\BuildingSeparateConceptRepository;
use App\Repository\SeparateConceptRepository;
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
    private ?float $percent = null;

    public function __construct()
    {
        $this->percent = 0;
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

    public function getPercent(): ?float
    {
        return $this->percent;
    }

    public function setPercent(float $percent): static
    {
        $this->percent = $percent;

        return $this;
    }

    public function getPrice(): float
    {
        return (float) $this->getBuilding()?->getConstructivePrice() * (float) $this->getPercent() / 100;
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

    public function getImport(?float $parentImport = null, ?float $parentPercent = null, ?SeparateConceptRepository $separateConceptRepository = null, ?BuildingSeparateConceptRepository $buildingSeparateConceptRepository = null): int|float
    {
        $import = (null === $parentImport) ? $this->getBuilding()?->getEstimatedAdjustValue() : $parentImport;

        $percent = (null === $parentPercent) ? $this->getPercent() : $parentPercent;

        return round((float) $import * (float) $percent / 100);
    }

    public function getPercentByFormula(float $import): float
    {
        return $import * 100 / (float) $this->getBuilding()?->getEstimatedAdjustValue();
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
