<?php

namespace App\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

class ArrayToCsvTransformer implements DataTransformerInterface
{
    private string $separator;

    public function __construct(string $separator = ',')
    {
        $this->separator = $separator;
    }

    /**
     * Transforma array a CSV (para mostrar en el formulario).
     */
    public function transform($array): string
    {
        if (null === $array || !is_array($array)) {
            return '';
        }

        // Limpiar y unir los valores
        $cleaned = array_filter($array, function ($value) {
            return !empty(trim($value));
        });

        return implode($this->separator, $cleaned);
    }

    /**
     * Transforma CSV a array (para guardar en la BD).
     */
    public function reverseTransform($csvString): array
    {
        if (null === $csvString || '' === trim($csvString)) {
            return [];
        }

        // Dividir por comas y limpiar
        $values = explode($this->separator, $csvString);

        // Limpiar espacios en blanco y eliminar valores vacíos
        $cleaned = array_map('trim', $values);
        $cleaned = array_filter($cleaned, function ($value) {
            return !empty($value);
        });

        return array_values($cleaned); // Reindexar el array
    }
}
