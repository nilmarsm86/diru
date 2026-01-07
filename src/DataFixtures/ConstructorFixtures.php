<?php

namespace App\DataFixtures;

use App\Entity\Constructor;
use App\Entity\Municipality;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ConstructorFixtures extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $constructors = ['Constructora1', 'Constructora2', 'Constructora3'];
        foreach ($constructors as $constructor) {
            $constructorEntity = $manager->getRepository(Constructor::class)->findOneBy(['name' => $constructor]);
            if (is_null($constructorEntity)) {
                $constructorEntity = new Constructor();
                $constructorEntity->setName($constructor);
                $constructorEntity->setCode($constructor.'-abc');
                $constructorEntity->setCountry('CU');
                $constructorEntity->setMunicipality($this->findMunicipality($manager));
                $constructorEntity->setStreet('direccion de la calle de la constructora');

                $manager->persist($constructorEntity);
            }
        }

        $manager->flush();
    }

    private function findMunicipality(ObjectManager $manager): ?Municipality
    {
        return $manager->getRepository(Municipality::class)->findOneBy(['name' => 'Playa']);
    }

    public function getDependencies(): array
    {
        return [
            ProvinceFixtures::class,
        ];
    }

    public static function getGroups(): array
    {
        return ['default'];
    }
}
