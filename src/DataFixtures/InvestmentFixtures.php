<?php

namespace App\DataFixtures;

use App\Entity\Building;
use App\Entity\Investment;
use App\Entity\LocationZone;
use App\Entity\Municipality;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class InvestmentFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $investments = ['Inversion1', 'Inversion2', 'Inversion3',];
        foreach ($investments as $investment) {
            $investmentEntity = $manager->getRepository(Investment::class)->findOneBy(['name' => $investment]);
            if (is_null($investmentEntity)) {
                $investmentEntity = new Investment();
                $investmentEntity->setName($investment);
                $investmentEntity->setStreet('calle');
                $investmentEntity->setMunicipality($this->findMunicipality($manager));
                $investmentEntity->setBlock('manzana');
                $investmentEntity->setAddressNumber(1);
                $investmentEntity->setBetweenStreets('entre calle A y calle B');
                $investmentEntity->setDistrict('circunscripcion');
                $investmentEntity->setLocationZone($this->findLocationZone($manager));
                $investmentEntity->setPopularCouncil('concejo popular');
                $investmentEntity->setTown('reparto_publo');


                $manager->persist($investmentEntity);
            }
        }

        $manager->flush();
    }

    private function findMunicipality(ObjectManager $manager): ?Municipality
    {
        return $manager->getRepository(Municipality::class)->findOneBy(['name' => 'Arroyo Naranjo']);
    }

    private function findLocationZone(ObjectManager $manager): ?LocationZone
    {
        return $manager->getRepository(LocationZone::class)->findOneBy(['name' => '1']);
    }



    public function getDependencies(): array
    {
        return [
            LocationZoneFixtures::class,
            ProvinceFixtures::class,
//            BuildingFixtures::class
        ];
    }
}
