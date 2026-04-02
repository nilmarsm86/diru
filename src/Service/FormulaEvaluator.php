<?php

namespace App\Service;

use InvalidArgumentException;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

final class FormulaEvaluator
{
    private ExpressionLanguage $expressionLanguage;

    public function __construct()
    {
        $this->expressionLanguage = new ExpressionLanguage();
    }

    /**
     * Evalúa una fórmula de forma segura.
     *
     * Las claves del array $datos son strings numéricos ("1", "2", "10", ...).
     * Los números en $noReemplazar también se tratan como strings.
     */
    /*public function evaluar(
        array $datos,
        string $formula,
        array $noReemplazar = [],
    ): float|int {
        if ('' === trim($formula)) {
            throw new \InvalidArgumentException('La fórmula no puede estar vacía.');
        }

        // Normalizar datos: claves como string, valores como float
        $datosNormalizados = [];
        foreach ($datos as $clave => $valor) {
            $claveStr = (string) $clave;

            if (!is_numeric($valor)) {
                throw new \InvalidArgumentException(sprintf('El valor de la clave "%s" no es numérico. Recibido: %s', $claveStr, gettype($valor)));
            }

            $datosNormalizados[$claveStr] = (float) $valor;
        }

        // Normalizar noReemplazar como strings
        $literales = array_map('strval', $noReemplazar);

        if (empty($datos)) {
            throw new InvalidArgumentException('No se encontraron claves en la fórmula.');
        }

        // Extraer todos los números (secuencias de dígitos) de la fórmula
        preg_match_all('/\b(\d+)\b/', $formula, $matches);
        $numerosEnFormula = array_unique($matches[1] ?? []);

        if (empty($numerosEnFormula)) {
            throw new \InvalidArgumentException('No se encontraron números en la fórmula.');
        }

        // Construir contexto para ExpressionLanguage y mapa de reemplazos
        $context = [];
        $replacements = []; // "123" => "var_0"
        $varCounter = 0;

        foreach ($numerosEnFormula as $numStr) {
            // Si está en noReemplazar → dejar como literal (prioridad alta)
            if (in_array($numStr, $literales, true)) {
                continue;
            }

            // Si existe en datos → reemplazar por variable segura
            if (array_key_exists($numStr, $datosNormalizados)) {
                $varName = 'var_'.$varCounter++;
                $context[$varName] = $datosNormalizados[$numStr];
                $replacements[$numStr] = $varName;
            }
            // Si no está en datos ni en noReemplazar → queda como número literal
        }

        // Realizar reemplazos seguros en la fórmula
        $formulaLista = $formula;
        foreach ($replacements as $numOriginal => $varName) {
            $formulaLista = preg_replace(
                '/\b'.preg_quote($numOriginal, '/').'\b/',
                $varName,
                $formulaLista
            );
        }

        // Evaluar de forma segura
        try {
            $resultado = $this->expressionLanguage->evaluate($formulaLista, $context);

            if (!is_numeric($resultado)) {
                throw new \RuntimeException('La expresión no devolvió un valor numérico válido.');
            }

            // Devolvemos int si es entero, float en caso contrario
            return (0.0 === fmod($resultado, 1)) ? (int) $resultado : (float) $resultado;
        } catch (\Exception $e) {
            throw new \RuntimeException(sprintf("Error al evaluar la fórmula.\nFórmula original: %s\nFórmula procesada: %s\nMensaje: %s", $formula, $formulaLista, $e->getMessage()), 0, $e);
        }
    }*/

    /**
     * @param array <mixed>  $datos
     * @param array <string> $noReemplazar
     */
    public function evaluar(
        array $datos,
        string $formula,
        array $noReemplazar = [],
    ): float|int {
        if ('' === trim($formula)) {
            throw new \InvalidArgumentException('La fórmula no puede estar vacía.');
        }

        $datosNormalizados = [];
        foreach ($datos as $clave => $valor) {
            $claveStr = (string) $clave;
            if (!is_numeric($valor)) {
                throw new \InvalidArgumentException("Valor no numérico en clave '{$claveStr}'");
            }
            $datosNormalizados[$claveStr] = (float) $valor;
        }

        $literales = array_map('strval', $noReemplazar);

        $numerosEnFormula = array_keys($datos);

        if (0 === count($numerosEnFormula)) {
            throw new \InvalidArgumentException('No se encontraron claves en la fórmula.');
        }

        $context = [];
        $replacements = []; // "4.1.1" => "var_0"
        $varCounter = 0;

        foreach ($numerosEnFormula as $numStr) {
            if (in_array($numStr, $literales, true)) {
                continue; // literal → se deja tal cual
            }

            if (array_key_exists($numStr, $datosNormalizados)) {
                $varName = 'var_'.$varCounter++;
                $context[$varName] = $datosNormalizados[$numStr];
                $replacements[$numStr] = $varName;
            }
        }

        // Reemplazo seguro
        $formulaLista = $formula;
        foreach ($replacements as $original => $varName) {
            // Usamos \b pero como hay puntos, mejor regex más preciso
            $formulaLista = preg_replace(
                '/(?<![\w.])'.preg_quote($original, '/').'(?![\w.])/',
                $varName,
                $formulaLista ?? ''
            );
        }

        // ←←← NUEVO: Proteger TODOS los números que tengan punto y sigan siendo literales
        // Esto evita que ExpressionLanguage los interprete mal
        $formulaLista = preg_replace_callback(
            '/\b(\d+(?:\.\d+)+)\b/',   // cualquier número con al menos un punto
            function ($match) use ($replacements) {
                $num = $match[1];
                // Si ya fue reemplazado por variable → no tocar
                if (in_array($num, array_keys($replacements), true)) {
                    return $match[0];
                }

                // Si es literal (incluidos los de noReemplazar) → rodear con paréntesis
                return '('.$num.')';
            },
            $formulaLista ?? ''
        ) ?? '';

        try {
            $resultado = $this->expressionLanguage->evaluate($formulaLista, $context);

            if (!is_numeric($resultado)) {
                throw new \RuntimeException('La expresión no devolvió valor numérico.');
            }

            return 0.0 === fmod((float) $resultado, 1) ? (int) $resultado : (float) $resultado;
        } catch (\Exception $e) {
            throw new \RuntimeException(sprintf("Error al evaluar la fórmula.\nOriginal: %s\nProcesada: %s\nMensaje: %s", $formula, $formulaLista, $e->getMessage()), 0, $e);
        }
    }
}
