<?php

namespace App\DataFixtures;

use App\Entity\Person;
use App\Entity\Representative;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Organism;

class RepresentativeFixtures extends Fixture implements FixtureGroupInterface
{
    public function load(ObjectManager $manager): void
    {
        $representatives = ['Representante 1', 'Representante 2', 'Representante 3', 'Representante 4'];
        foreach ($representatives as $representatives){
            $personEntity = $manager->getRepository(Representative::class)->findOneBy(['name' => $representatives]);
            if(is_null($personEntity)){
                $personEntity = new Representative();
                $personEntity->setName($representatives);
                $personEntity->setLastname("Apellido1 Apellido2");
                $personEntity->setIdentificationNumber(rand(11111111111, 99999999999));
                $personEntity->setPhone(rand(50000000, 69999999));
                $personEntity->setEmail(strtolower(str_replace(' ', '_', $representatives)).'@diru.com');
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
