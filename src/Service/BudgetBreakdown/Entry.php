<?php

namespace App\Service\BudgetBreakdown;

final readonly class Entry
{
    private function __construct(
        private string $import,
        private string $percent,
    ) {
    }

    public static function create(string $import = '0.00', string $percent = '0.00'): self
    {
        self::assertValidDecimal($import, 'import');
        self::assertValidDecimal($percent, 'percent');

        return new self(
            import: self::normalizeDecimal($import),
            percent: self::normalizeDecimal($percent),
        );
    }

    /**
     * @param array <mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $import = $data['import'] ?? '0.00';
        $percent = $data['percent'] ?? '0.00';

        if (!is_string($import) || !is_string($percent)) {
            throw new \InvalidArgumentException("Entry fields 'import' and 'percent' must be strings.");
        }

        return self::create($import, $percent);
    }

    public function getImport(): string
    {
        return $this->import;
    }

    public function getPercent(): string
    {
        return $this->percent;
    }

    public function withImport(string $import): self
    {
        return self::create($import, $this->percent);
    }

    public function withPercent(string $percent): self
    {
        return self::create($this->import, $percent);
    }

    /**
     * @return array<mixed>
     */
    public function toArray(): array
    {
        return [
            'import' => $this->import,
            'percent' => $this->percent,
        ];
    }

    public function importAsFloat(): float
    {
        return (float) $this->import;
    }

    public function percentAsFloat(): float
    {
        return (float) $this->percent;
    }

    // ── Private helpers ──────────────────────────────────────────────────────

    private static function assertValidDecimal(string $value, string $field): void
    {
        if (false === preg_match('/^\d+(\.\d+)?$/', $value)) {
            throw new \InvalidArgumentException("Field '{$field}' must be a valid non-negative decimal string. Got: '{$value}'.");
        }
    }

    private static function normalizeDecimal(string $value): string
    {
        return number_format((float) $value, 2, '.', '');
    }
}
