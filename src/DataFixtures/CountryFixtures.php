<?php

namespace App\DataFixtures;

use App\Entity\City;
use App\Entity\Country;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class CountryFixtures extends Fixture implements FixtureGroupInterface
{
    public function load(ObjectManager $manager): void
    {
        $countryWithCities = [
            'Cuba' => ['Habana'],
        ];

        foreach ($countryWithCities as $countryName => $cities) {
            $country = $manager->getRepository(Country::class)->findOneBy(['name' => $countryName]);

            if (null === $country) {
                $this->addCountryCity($manager, $countryName, $cities);
            }
        }

        $manager->flush();
    }

    /**
     * @param array<string> $cities
     */
    public function addCountryCity(ObjectManager $manager, string $countryName, array $cities): bool
    {
        $country = (new Country())->setName($countryName);
        foreach ($cities as $city) {
            $country->addCity((new City())->setName($city));
        }

        $manager->persist($country);

        return true;
    }

    public static function getGroups(): array
    {
        return ['default'];
    }

    public function getOrder(): int
    {
        return -1;
    }
}
