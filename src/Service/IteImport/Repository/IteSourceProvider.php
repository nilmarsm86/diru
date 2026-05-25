<?php

declare(strict_types=1);

namespace App\Service\IteImport\Repository;

use App\Entity\IteSource;
use App\Repository\IteSourceRepository;

final class IteSourceProvider
{
    private const FIELD_NAME = 'name';

    /** @var array<string, IteSource> */
    private array $cache = [];

    public function __construct(
        private readonly IteSourceRepository $iteSourceRepository,
    ) {
    }

    // TODO: se puede aplicar el metodo plantilla
    public function getByName(string $name): IteSource
    {
        if (isset($this->cache[$name])) {
            return $this->cache[$name];
        }

        $source = $this->iteSourceRepository->findOneBy([self::FIELD_NAME => $name]);

        if (null === $source) {
            $source = new IteSource();
            $source->setName($name);

            $this->iteSourceRepository->save($source, true);
        }

        return $this->cache[$name] = $source;
    }
}
