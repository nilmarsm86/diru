<?php

declare(strict_types=1);

namespace App\Service\IteImport\Repository;

use App\Entity\IteProjectType;
use App\Repository\IteProjectTypeRepository;
use App\Service\IteImport\Cache\LocalCacheTrait;

final class IteProjectTypeProvider
{
    use LocalCacheTrait;

    private const FIELD_NAME = 'name';

    public function __construct(
        private readonly IteProjectTypeRepository $iteProjectTypeRepository,
    ) {
    }

    public function getByName(string $name): IteProjectType
    {
        /** @var IteProjectType $iteProjectType */
        $iteProjectType = $this->getCached($name, function () use ($name): IteProjectType {
            return $this->getOrCreate($name);
        });

        return $iteProjectType;
    }

    private function getOrCreate(string $name): IteProjectType
    {
        $iteProjectType = $this->iteProjectTypeRepository->findOneBy([self::FIELD_NAME => $name]);

        if (null === $iteProjectType) {
            $iteProjectType = new IteProjectType();
            $iteProjectType->setName($name);

            $this->iteProjectTypeRepository->save($iteProjectType);
        }

        return $iteProjectType;
    }
}
