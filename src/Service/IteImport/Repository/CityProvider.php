<?php

declare(strict_types=1);

namespace App\Service\IteImport\Repository;

use App\Entity\City;
use App\Repository\CityRepository;
use App\Service\IteImport\Cache\LocalCacheTrait;

final class CityProvider
{
    use LocalCacheTrait;

    private const FIELD_NAME = 'name';

    public function __construct(
        private readonly CityRepository $cityRepository,
        private readonly CountryProvider $countryProvider,
    ) {
    }

    public function getByName(string $cityName, string $countryName): City
    {
        $cacheKey = sprintf('%s_%s', $countryName, $cityName);

        /** @var City $city */
        $city = $this->getCached($cacheKey, function () use ($cityName, $countryName): City {
            return $this->getOrCreate($cityName, $countryName);
        });

        return $city;
    }

    private function getOrCreate(string $cityName, string $countryName): City
    {
        $city = $this->cityRepository->findOneBy([self::FIELD_NAME => $cityName]);

        if (null === $city) {
            $city = new City();
            $city->setName($cityName);
        }

        // Delegamos la responsabilidad del país a quien le corresponde
        $country = $this->countryProvider->getByName($countryName);

        // Relacionamos
        $country->addCity($city);
        $this->cityRepository->save($city);

        return $city;
    }
}
