<?php

declare(strict_types=1);

namespace App\Service\IteImport\Reader;

/**
 * Selecciona el Reader que entiende los headers de una hoja Excel concreta.
 *
 * Symfony inyecta automáticamente todos los servicios que implementen
 * {@see IteExcelReaderInterface} gracias al atributo #[AutowireIterator]
 * (alternativa: tag 'app.ite_excel_reader').
 *
 * Si ningún Reader aplica devuelve null, dejando al orquestador decidir
 * si saltar la hoja o reportarlo como error.
 */
final readonly class IteReaderResolver
{
    /**
     * @param iterable<IteExcelReaderInterface> $readers
     */
    public function __construct(
        private iterable $readers,
    ) {
    }

    /**
     * @param list<string> $normalizedHeaders
     */
    public function resolve(array $normalizedHeaders): ?IteExcelReaderInterface
    {
        foreach ($this->readers as $reader) {
            if ($reader->supports($normalizedHeaders)) {
                return $reader;
            }
        }

        return null;
    }
}
