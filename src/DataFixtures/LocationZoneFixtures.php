<?php

namespace App\DataFixtures;

use App\Entity\LocationZone;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class LocationZoneFixtures extends Fixture implements FixtureGroupInterface
{
    public function load(ObjectManager $manager): void
    {
        $zones = ['1', '2', '3', '4', '5'];
        foreach ($zones as $zone) {
            $locationZoneEntity = $manager->getRepository(LocationZone::class)->findOneBy(['name' => $zone]);
            if (is_null($locationZoneEntity)) {
                $locationZoneEntity = new LocationZone();
                $locationZoneEntity->setName($zone);

                $manager->persist($locationZoneEntity);
            }
        }

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['default'];
    }
}
