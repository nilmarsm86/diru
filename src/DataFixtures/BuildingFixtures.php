<?php

namespace App\DataFixtures;

use App\Entity\Building;
use App\Entity\Constructor;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class BuildingFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $buildings = ['Obra1', 'Obra2', 'Obra3',];
        foreach ($buildings as $building) {
            $buildingEntity = $manager->getRepository(Building::class)->findOneBy(['name' => $building]);
            if (is_null($buildingEntity)) {
                $buildingEntity = new Building();
                $buildingEntity->setName($building);
                $buildingEntity->setConstructor($this->findConstructor($manager));
                $buildingEntity->setApprovedValueConstruction(1000000);
                $buildingEntity->setApprovedValueEquipment(1000000);
                $buildingEntity->setApprovedValueOther(1000000);
                $buildingEntity->setEstimatedValueConstruction(1000000);
                $buildingEntity->setEstimatedValueEquipment(1000000);
                $buildingEntity->setEstimatedValueOther(1000000);

                $manager->persist($buildingEntity);
            }
        }

        $manager->flush();
    }

    private function findConstructor(ObjectManager $manager): ?Constructor
    {
        return $manager->getRepository(Constructor::class)->findOneBy(['name' => 'Constructora1']);
    }

    public function getDependencies(): array
    {
        return [
            ConstructorFixtures::class,
        ];
    }
}
