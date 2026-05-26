<?php

declare(strict_types=1);

namespace App\Service\IteImport\Cache;

trait LocalCacheTrait
{
    /** @var array<string, mixed> */
    private array $cache = [];

    private function getCached(string $key, callable $resolver): mixed
    {
        if (isset($this->cache[$key])) {
            return $this->cache[$key];
        }

        return $this->cache[$key] = $resolver();
    }
}
