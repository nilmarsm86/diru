<?php

namespace App\DataFixtures;

use App\Entity\Representative;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class RepresentativeFixtures extends Fixture implements FixtureGroupInterface
{
    public function load(ObjectManager $manager): void
    {
        $representatives = ['Representante 1', 'Representante 2', 'Representante 3', 'Representante 4'];
        foreach ($representatives as $representative) {
            $personEntity = $manager->getRepository(Representative::class)->findOneBy(['name' => $representative]);
            if (is_null($personEntity)) {
                $personEntity = new Representative();
                $personEntity->setName($representative);
                $personEntity->setLastname('Apellido1 Apellido2');
                $personEntity->setIdentificationNumber((string) rand(11111111111, 99999999999));
                $personEntity->setPhone((string) rand(50000000, 69999999));
                $personEntity->setEmail(strtolower(str_replace(' ', '_', $representative)).'@diru.com');
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
