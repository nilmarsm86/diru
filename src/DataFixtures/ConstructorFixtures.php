<?php

namespace App\DataFixtures;

use App\Entity\Constructor;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class ConstructorFixtures extends Fixture implements FixtureGroupInterface
{
    public function load(ObjectManager $manager): void
    {
        $constructors = ['Constructora1', 'Constructora2', 'Constructora3',];
        foreach ($constructors as $constructor) {
            $constructorEntity = $manager->getRepository(Constructor::class)->findOneBy(['name' => $constructor]);
            if (is_null($constructorEntity)) {
                $constructorEntity = new Constructor();
                $constructorEntity->setName($constructor);
                $constructorEntity->setCode($constructor.'-abc');
                $constructorEntity->setCountry('CU');

                $manager->persist($constructorEntity);
            }
        }

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['default'];
    }
}
