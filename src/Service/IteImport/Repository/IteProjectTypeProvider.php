<?php

declare(strict_types=1);

namespace App\Service\IteImport\Repository;

use App\Entity\IteProjectType;
use App\Repository\IteProjectTypeRepository;

final class IteProjectTypeProvider
{
    private const FIELD_NAME = 'name';

    /** @var array<string, IteProjectType> */
    private array $cache = [];

    public function __construct(
        private readonly IteProjectTypeRepository $iteProjectTypeRepository,
    ) {
    }

    // TODO: se puede aplicar el metodo plantilla
    public function getByName(string $name): IteProjectType
    {
        if (isset($this->cache[$name])) {
            return $this->cache[$name];
        }

        $iteProjectType = $this->iteProjectTypeRepository->findOneBy([self::FIELD_NAME => $name]);

        if (null === $iteProjectType) {
            $iteProjectType = new IteProjectType();
            $iteProjectType->setName($name);

            $this->iteProjectTypeRepository->save($iteProjectType, true);
        }

        return $this->cache[$name] = $iteProjectType;
    }
}
