<?php

declare(strict_types=1);

namespace App\Service\IteImport\Repository;

use App\Entity\Country;
use App\Repository\CountryRepository;
use App\Service\IteImport\Cache\LocalCacheTrait;

final class CountryProvider
{
    use LocalCacheTrait;

    private const FIELD_NAME = 'name';

    public function __construct(
        private readonly CountryRepository $countryRepository,
    ) {
    }

    public function getByName(string $name): Country
    {
        /** @var Country $country */
        $country = $this->getCached($name, function () use ($name): Country {
            return $this->getOrCreate($name);
        });

        return $country;
    }

    private function getOrCreate(string $name): Country
    {
        $country = $this->countryRepository->findOneBy([self::FIELD_NAME => $name]);

        if (null === $country) {
            $country = new Country();
            $country->setName($name);

            $this->countryRepository->save($country);
        }

        return $country;
    }
}
