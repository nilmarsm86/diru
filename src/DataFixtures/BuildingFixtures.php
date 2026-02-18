<?php

namespace App\DataFixtures;

use App\Entity\Building;
use App\Entity\Client;
use App\Entity\Constructor;
use App\Entity\CorporateEntity;
use App\Entity\Draftsman;
use App\Entity\EnterpriseClient;
use App\Entity\IndividualClient;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class BuildingFixtures extends Fixture implements DependentFixtureInterface, FixtureGroupInterface
{
    public function load(ObjectManager $manager): void
    {
        $buildings = ['Obra1', 'Obra2', 'Obra3'];
        foreach ($buildings as $building) {
            $buildingEntity = $manager->getRepository(Building::class)->findOneBy(['name' => $building]);
            if (is_null($buildingEntity)) {
                $buildingEntity = new Building();
                $buildingEntity->setName($building);

                $constructor = $this->findConstructor($manager);
                if (null !== $constructor) {
                    $buildingEntity->addConstructor($constructor);
                }

                $corporateEntity = $this->findCorporateEntity($manager);
                if (null !== $corporateEntity) {
                    $buildingEntity->addCorporateEntity($corporateEntity);
                }

                $buildingEntity->setApprovedValueConstruction(1000000);
                $buildingEntity->setApprovedValueEquipment(1000000);
                $buildingEntity->setApprovedValueOther(1000000);
                $buildingEntity->setEstimatedValueEquipment(1000000);
                $buildingEntity->setEstimatedValueOther(1000000);
                $buildingEntity->setPopulation(1);

                if ('Obra1' === $building) {
                    $draftsman = $this->findDraftsman($manager, 'Draftsman');
                    if (null !== $draftsman) {
                        $buildingEntity->addDraftsman($draftsman);
                    }

                    $buildingEntity->setClient($this->findClient($manager));
                } else {
                    $buildingEntity->setClient($this->findClient($manager, false));
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

    private function findCorporateEntity(ObjectManager $manager): ?CorporateEntity
    {
        return $manager->getRepository(CorporateEntity::class)->findOneBy(['name' => 'Entidad corporativa 1']);
    }

    private function findDraftsman(ObjectManager $manager, string $name): ?Draftsman
    {
        return $manager->getRepository(Draftsman::class)->findOneBy(['name' => $name]);
    }

    private function findClient(ObjectManager $manager, bool $enterprise = true): Client
    {
        $entityClass = ($enterprise) ? EnterpriseClient::class : IndividualClient::class;
        $clients = $manager->getRepository($entityClass)->findAll();

        return $clients[0];
    }

    public function getDependencies(): array
    {
        return [
            ConstructorFixtures::class,
            CorporateEntityFixtures::class,
            UserFixtures::class,
        ];
    }

    public static function getGroups(): array
    {
        return ['default'];
    }
}
