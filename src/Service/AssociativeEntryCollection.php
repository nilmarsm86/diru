<?php

namespace App\Service;

/**
 * Manages an associative array with structure:
 * ['number' => ['import' => '0.00', 'percent' => '0.00']]
 *
 * @implements \IteratorAggregate<string, Entry>
 */
final class AssociativeEntryCollection implements \Countable, \IteratorAggregate
{
    /** @var array<string, Entry> */
    private array $entries = [];

    // ── Factory ──────────────────────────────────────────────────────────────

    public static function empty(): self
    {
        return new self();
    }

    //    /**
    //     * @param array<string|int, array{import: string, percent: string}> $raw
    //     */
    //    public static function fromArray(array $raw): self
    //    {
    //        $collection = new self();
    //
    //        foreach ($raw as $number => $data) {
    //            $collection->set((string) $number, Entry::fromArray($data));
    //        }
    //
    //        return $collection;
    //    }

    // ── Mutation ─────────────────────────────────────────────────────────────

    public function set(string $number, Entry $entry): self
    {
        $this->assertValidNumber($number);
        $this->entries[$number] = $entry;

        return $this;
    }

    public function add(string $number, string $import = '0.00', string $percent = '0.00'): self
    {
        return $this->set($number, Entry::create($import, $percent));
    }

    //    public function remove(string $number): self
    //    {
    //        $this->assertExists($number);
    //        unset($this->entries[$number]);
    //
    //        return $this;
    //    }

    //    public function updateImport(string $number, string $import): self
    //    {
    //        $this->assertExists($number);
    //        $this->entries[$number] = $this->entries[$number]->withImport($import);
    //
    //        return $this;
    //    }

    //    public function updatePercent(string $number, string $percent): self
    //    {
    //        $this->assertExists($number);
    //        $this->entries[$number] = $this->entries[$number]->withPercent($percent);
    //
    //        return $this;
    //    }

    //    public function clear(): self
    //    {
    //        $this->entries = [];
    //
    //        return $this;
    //    }

    // ── Query ────────────────────────────────────────────────────────────────

    //    public function get(string $number): Entry
    //    {
    //        $this->assertExists($number);
    //
    //        return $this->entries[$number];
    //    }

    public function find(string $number): ?Entry
    {
        return $this->entries[$number] ?? null;
    }

    //    public function has(string $number): bool
    //    {
    //        return isset($this->entries[$number]);
    //    }

    /**
     * @return array<mixed>
     */
    public function keys(): array
    {
        return array_keys($this->entries);
    }

    //    /** @return array<string, Entry> */
    //    public function all(): array
    //    {
    //        return $this->entries;
    //    }

    //    public function isEmpty(): bool
    //    {
    //        return count($this->entries) === 0;
    //    }

    // ── Aggregates ───────────────────────────────────────────────────────────

    //    public function totalImport(): string
    //    {
    //        $total = array_reduce(
    //            $this->entries,
    //            fn (float $carry, Entry $entry) => $carry + $entry->importAsFloat(),
    //            0.0,
    //        );
    //
    //        return number_format($total, 2, '.', '');
    //    }

    //    public function totalPercent(): string
    //    {
    //        $total = array_reduce(
    //            $this->entries,
    //            fn (float $carry, Entry $entry) => $carry + $entry->percentAsFloat(),
    //            0.0,
    //        );
    //
    //        return number_format($total, 2, '.', '');
    //    }

    //    public function averageImport(): string
    //    {
    //        if ($this->isEmpty()) {
    //            return '0.00';
    //        }
    //
    //        $average = (float) $this->totalImport() / $this->count();
    //
    //        return number_format($average, 2, '.', '');
    //    }

    // ── Functional ───────────────────────────────────────────────────────────

    //    /**
    //     * @param callable(Entry, string): bool $predicate
    //     */
    //    public function filter(callable $predicate): self
    //    {
    //        $clone = clone $this;
    //        $clone->entries = array_filter(
    //            $this->entries,
    //            fn (Entry $entry, string $number) => $predicate($entry, $number),
    //            ARRAY_FILTER_USE_BOTH,
    //        );
    //
    //        return $clone;
    //    }

    //    /**
    //     * @param callable(Entry, string): Entry $transform
    //     */
    //    public function map(callable $transform): self
    //    {
    //        $clone = clone $this;
    //        $clone->entries = array_map(
    //            fn (Entry $entry, string $number) => $transform($entry, $number),
    //            $this->entries,
    //            array_keys($this->entries),
    //        );
    //
    //        return $clone;
    //    }

    //    /**
    //     * @param callable(Entry, string): void $callback
    //     */
    //    public function each(callable $callback): self
    //    {
    //        foreach ($this->entries as $number => $entry) {
    //            $callback($entry, $number);
    //        }
    //
    //        return $this;
    //    }

    // ── Serialization ────────────────────────────────────────────────────────

    /**
     * Returns the raw associative array format.
     *
     * @return array<mixed>
     */
    public function toArray(): array
    {
        return array_map(
            fn (Entry $entry) => $entry->toArray(),
            $this->entries,
        );
    }

    //    /**
    //     * @throws \JsonException
    //     */
    //    public function toJson(int $flags = JSON_PRETTY_PRINT): string
    //    {
    //        return json_encode($this->toArray(), $flags | JSON_THROW_ON_ERROR);
    //    }

    // ── Interfaces ───────────────────────────────────────────────────────────

    public function count(): int
    {
        return count($this->entries);
    }

    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->entries);
    }

    // ── Guards ───────────────────────────────────────────────────────────────

    private function assertValidNumber(string $number): void
    {
        if ('' === trim($number)) {
            throw new \InvalidArgumentException('Entry key (number) cannot be empty.');
        }
    }

    //    private function assertExists(string $number): void
    //    {
    //        if (!$this->has($number)) {
    //            throw new \OutOfBoundsException("Entry with number '{$number}' does not exist.");
    //        }
    //    }
}
