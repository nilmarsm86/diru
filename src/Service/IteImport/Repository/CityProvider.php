<?php

declare(strict_types=1);

namespace App\Service\IteImport\Repository;

use App\Entity\City;
use App\Entity\Country;
use App\Repository\CityRepository;
use App\Repository\CountryRepository;

final class CityProvider
{
    private const FIELD_NAME = 'name';

    /** @var array<string, City> */
    private array $cache = [];

    public function __construct(
        private readonly CityRepository $cityRepository,
        private readonly CountryRepository $countryRepository,
    ) {
    }

    // TODO: se puede aplicar el metodo plantilla
    public function getByName(string $cityName, string $countryName): City
    {
        if (isset($this->cache[$cityName])) {
            return $this->cache[$cityName];
        }

        $city = $this->cityRepository->findOneBy([self::FIELD_NAME => $cityName]);
        if (null === $city) {
            $city = new City();
            $city->setName($cityName);
        }

        $country = $this->countryRepository->findOneBy([self::FIELD_NAME => $countryName]);
        if (null === $country) {
            $country = new Country();
            $country->setName($countryName);
        }

        $country->addCity($city);
        $this->countryRepository->save($country, true);

        return $this->cache[$cityName] = $city;
    }
}
