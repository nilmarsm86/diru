<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

final readonly class FormulaEvaluator
{
    public function __construct(
        #[Autowire(service: ExpressionLanguage::class)]
        private ExpressionLanguage $expressionLanguage,
    ) {
    }

    /**
     * @param array<string> $datos
     */
    public function evaluar(array $datos, string $formula): float|int
    {
        $this->validarFormula($formula);

        $datosNormalizados = $this->normalizarDatos($datos);
        $claves = array_keys($datosNormalizados);

        $this->validarClaves($claves);

        [$context, $replacements] = $this->construirContexto($claves, $datosNormalizados);

        $formulaProcesada = $this->procesarFormula($formula, $replacements);

        return $this->ejecutarEvaluacion($formulaProcesada, $context, $formula);
    }

    // -------------------------------------------------------------------------
    // Validaciones
    // -------------------------------------------------------------------------

    private function validarFormula(string $formula): void
    {
        if ('' === trim($formula)) {
            throw new \InvalidArgumentException('La fórmula no puede estar vacía.');
        }
    }

    /**
     * @param array<string> $claves
     */
    private function validarClaves(array $claves): void
    {
        if (0 === count($claves)) {
            throw new \InvalidArgumentException('No se encontraron claves en la fórmula.');
        }
    }

    // -------------------------------------------------------------------------
    // Normalización de datos
    // -------------------------------------------------------------------------

    /**
     * @param array<string> $datos
     *
     * @return array<string, float>
     */
    private function normalizarDatos(array $datos): array
    {
        $normalizados = [];

        foreach ($datos as $clave => $valor) {
            $claveStr = (string) $clave;

            if (!is_numeric($valor)) {
                throw new \InvalidArgumentException("Valor no numérico en clave '{$claveStr}'");
            }

            $normalizados[$claveStr] = (float) $valor;
        }

        return $normalizados;
    }

    // -------------------------------------------------------------------------
    // Construcción del contexto de evaluación
    // -------------------------------------------------------------------------

    /**
     * Mapea cada clave de datos a una variable segura (var_0, var_1 …)
     * para que ExpressionLanguage no interprete claves con puntos como decimales.
     *
     * @param array<string>        $claves
     * @param array<string, float> $datosNormalizados
     *
     * @return array{0: array<string,float>, 1: array<string,string>}
     */
    private function construirContexto(array $claves, array $datosNormalizados): array
    {
        $context = [];
        $replacements = [];
        $varCounter = 0;

        foreach ($claves as $clave) {
            if (!array_key_exists($clave, $datosNormalizados)) {
                continue;
            }

            $varName = 'var_'.$varCounter++;
            $context[$varName] = $datosNormalizados[$clave];
            $replacements[$clave] = $varName;
        }

        return [$context, $replacements];
    }

    // -------------------------------------------------------------------------
    // Procesado de la fórmula
    // -------------------------------------------------------------------------

    /**
     * @param array<string> $replacements
     */
    private function procesarFormula(string $formula, array $replacements): string
    {
        $formula = $this->reemplazarClaves($formula, $replacements);
        $formula = $this->protegerNumerosConPunto($formula, $replacements);

        return $formula;
    }

    /**
     * @param array<string> $replacements
     */
    private function reemplazarClaves(string $formula, array $replacements): string
    {
        foreach ($replacements as $original => $varName) {
            $formula = preg_replace(
                '/(?<![\w.])'.preg_quote($original, '/').'(?![\w.])/',
                $varName,
                $formula ?? ''
            );
        }

        return $formula ?? '';
    }

    /**
     * Rodea con paréntesis los literales numéricos con punto (ej: 4.1.1)
     * que no hayan sido sustituidos por variable, para evitar que
     * ExpressionLanguage los malinterprete.
     */
    /**
     * @param array<string> $replacements
     */
    private function protegerNumerosConPunto(string $formula, array $replacements): string
    {
        return preg_replace_callback(
            '/\b(\d+(?:\.\d+)+)\b/',
            fn (array $match) => $this->envolverLiteralNumerico($match[1], $replacements),
            $formula
        ) ?? '';
    }

    /**
     * @param array<string> $replacements
     */
    private function envolverLiteralNumerico(string $numero, array $replacements): string
    {
        $yaReemplazado = in_array($numero, array_keys($replacements), true);

        return $yaReemplazado ? $numero : '('.$numero.')';
    }

    // -------------------------------------------------------------------------
    // Evaluación
    // -------------------------------------------------------------------------

    /**
     * @param array<mixed> $context
     */
    private function ejecutarEvaluacion(string $formulaProcesada, array $context, string $formulaOriginal): float|int
    {
        try {
            $resultado = $this->expressionLanguage->evaluate($formulaProcesada, $context);

            if (!is_numeric($resultado)) {
                throw new \RuntimeException('La expresión no devolvió valor numérico.');
            }

            return $this->castearResultado((float) $resultado);
        } catch (\Exception $e) {
            throw new \RuntimeException(sprintf("Error al evaluar la fórmula.\nOriginal: %s\nProcesada: %s\nMensaje: %s", $formulaOriginal, $formulaProcesada, $e->getMessage()), 0, $e);
        }
    }

    private function castearResultado(float $resultado): float|int
    {
        return 0.0 === fmod($resultado, 1) ? (int) $resultado : $resultado;
    }
}
