<?php

namespace App\DataFixtures;

use App\Entity\Person;
use App\Entity\Representative;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Organism;

class PersonFixtures extends Fixture implements FixtureGroupInterface
{
    public function load(ObjectManager $manager): void
    {
        $persons = ['Person 1', 'Person 2', 'Person 3', 'Person 4'];
        foreach ($persons as $person){
            $personEntity = $manager->getRepository(Representative::class)->findOneBy(['name' => $person]);
            if(is_null($personEntity)){
                $personEntity = new Person();
                $personEntity->setName($person);
                $personEntity->setLastname("Apellido1 Apellido2");
                $personEntity->setIdentificationNumber(rand(11111111111, 99999999999));
                $manager->persist($personEntity);
            }
        }

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['default'];
    }
}
