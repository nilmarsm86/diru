<?php

namespace App\DataFixtures;

use App\Entity\Building;
use App\Entity\Constructor;
use App\Entity\Draftsman;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class BuildingFixtures extends Fixture implements DependentFixtureInterface, FixtureGroupInterface
{
    public function load(ObjectManager $manager): void
    {
        $buildings = ['Obra1', 'Obra2', 'Obra3',];
        foreach ($buildings as $building) {
            $buildingEntity = $manager->getRepository(Building::class)->findOneBy(['name' => $building]);
            if (is_null($buildingEntity)) {
                $buildingEntity = new Building();
                $buildingEntity->setName($building);
                if($constructor = $this->findConstructor($manager)){
                    $buildingEntity->addConstructor($constructor);
                }
                $buildingEntity->setApprovedValueConstruction(1000000);
                $buildingEntity->setApprovedValueEquipment(1000000);
                $buildingEntity->setApprovedValueOther(1000000);
                $buildingEntity->setEstimatedValueConstruction(0);
                $buildingEntity->setEstimatedValueEquipment(1000000);
                $buildingEntity->setEstimatedValueOther(1000000);
                $buildingEntity->setProjectPriceTechnicalPreparation(0);
                $buildingEntity->setPopulation(1);

                if($building === 'Obra1'){
                    if($draftsman = $this->findDraftsman($manager, 'Draftsman')){
                        $buildingEntity->addDraftsman($draftsman);
                    }
                }

                $manager->persist($buildingEntity);
            }
        }

        $manager->flush();
    }

    private function findConstructor(ObjectManager $manager): ?Constructor
    {
        return $manager->getRepository(Constructor::class)->findOneBy(['name' => 'Constructora1']);
    }

    private function findDraftsman(ObjectManager $manager, string $name): ?Draftsman
    {
        return $manager->getRepository(Draftsman::class)->findOneBy(['name' => $name]);
    }

    public function getDependencies(): array
    {
        return [
            ConstructorFixtures::class,
            UserFixtures::class,
        ];
    }

    public static function getGroups(): array
    {
        return ['default'];
    }
}
