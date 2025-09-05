<?php

namespace App\DataFixtures;

use App\Entity\Building;
use App\Entity\Constructor;
use App\Entity\Draftsman;
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
                $buildingEntity->addConstructor($this->findConstructor($manager));
                $buildingEntity->setApprovedValueConstruction(1000000);
                $buildingEntity->setApprovedValueEquipment(1000000);
                $buildingEntity->setApprovedValueOther(1000000);
                $buildingEntity->setEstimatedValueConstruction(1000000);
                $buildingEntity->setEstimatedValueEquipment(1000000);
                $buildingEntity->setEstimatedValueOther(1000000);
                $buildingEntity->setPopulation(1);

                if($building === 'Obra1'){
                    $buildingEntity->addDraftsman($this->findDraftsman($manager, 'Draftsman'));
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
}
