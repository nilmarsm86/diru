<?php

declare(strict_types=1);

namespace App\Service\IteImport\Repository;

use App\Entity\IteSource;
use App\Repository\IteSourceRepository;
use App\Service\IteImport\Cache\LocalCacheTrait;

final class IteSourceProvider
{
    use LocalCacheTrait;

    private const FIELD_NAME = 'name';

    public function __construct(
        private readonly IteSourceRepository $iteSourceRepository,
    ) {
    }

    public function getByName(string $name): IteSource
    {
        /** @var IteSource $iteSource */
        $iteSource = $this->getCached($name, function () use ($name): IteSource {
            return $this->getOrCreate($name);
        });

        return $iteSource;
    }

    private function getOrCreate(string $name): IteSource
    {
        $source = $this->iteSourceRepository->findOneBy([self::FIELD_NAME => $name]);

        if (null === $source) {
            $source = new IteSource();
            $source->setName($name);

            $this->iteSourceRepository->save($source);
        }

        return $source;
    }
}
