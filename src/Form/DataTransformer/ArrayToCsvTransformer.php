<?php

namespace App\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

/**
 * @implements DataTransformerInterface<array<int, string>, string>
 */
class ArrayToCsvTransformer implements DataTransformerInterface
{
    private readonly string $separator;

    public function __construct(string $separator = ',')
    {
        if ('' === $separator) {
            throw new \InvalidArgumentException('El separador no puede estar vacío.');
        }

        $this->separator = $separator;
    }

    /**
     * Transforma array a CSV (para mostrar en el formulario).
     *
     * @param array<int, string>|null $array
     */
    public function transform(mixed $array): string
    {
        if (!is_array($array)) {
            return '';
        }

        // Filtrar valores que no sean strings o estén vacíos
        $cleaned = array_filter($array, function (mixed $value): bool {
            return '' !== trim($value);
        });

        return implode($this->separator, $cleaned);
    }

    /**
     * Transforma CSV a array (para guardar en la BD).
     *
     * @return array<int, string>
     */
    public function reverseTransform(mixed $csvString): array
    {
        // Validar que es un string
        if (!is_string($csvString)) {
            // Si es null o cualquier otra cosa, retornar array vacío
            return [];
        }

        $csvString = trim($csvString);

        // Si está vacío después de trim, retornar array vacío
        if ('' === $csvString) {
            return [];
        }

        // Dividir por separador (ahora $this->separator es non-empty-string seguro)
        $values = explode($this->separator, $csvString);

        // Limpiar espacios y filtrar valores vacíos
        $cleaned = array_map('trim', $values);
        $cleaned = array_filter($cleaned, function (string $value): bool {
            return '' !== $value;
        });

        return array_values($cleaned);
    }
}
