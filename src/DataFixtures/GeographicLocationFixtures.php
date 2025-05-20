<?php

namespace App\DataFixtures;

use App\Entity\GeographicLocation;
use App\Entity\LocationZone;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Organism;

class GeographicLocationFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $locations = ['1', '2', '3', '4', '5'];
        foreach ($locations as $location){
            $glEntity = $manager->getRepository(GeographicLocation::class)->findOneBy(['name' => $location]);
            if(is_null($glEntity)){
                $glEntity = new GeographicLocation();
                $glEntity->setName($location);

                $manager->persist($glEntity);
            }
        }

        $manager->flush();
    }
}
