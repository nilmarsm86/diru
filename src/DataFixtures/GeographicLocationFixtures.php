<?php

namespace App\DataFixtures;

use App\Entity\GeographicLocation;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class GeographicLocationFixtures extends Fixture implements FixtureGroupInterface
{
    public function load(ObjectManager $manager): void
    {
        $locations = ['1', '2', '3', '4', '5'];
        foreach ($locations as $location) {
            $glEntity = $manager->getRepository(GeographicLocation::class)->findOneBy(['name' => $location]);
            if (is_null($glEntity)) {
                $glEntity = new GeographicLocation();
                $glEntity->setName($location);

                $manager->persist($glEntity);
            }
        }

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['default'];
    }
}
