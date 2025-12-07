<?php

namespace App\DataFixtures;

use App\Entity\CorporateEntity;
use App\Entity\EnterpriseClient;
use App\Entity\IndividualClient;
use App\Entity\Municipality;
use App\Entity\Person;
use App\Entity\Representative;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class EnterpriseClientFixtures extends Fixture implements DependentFixtureInterface, FixtureGroupInterface
{
    public function load(ObjectManager $manager): void
    {
        $clients = ['Cliente empresarial 3', 'Cliente empresarial 4'];
        $representatives = ['Representante 1', 'Representante 2', 'Representante 3', 'Representante 4'];
        foreach ($clients as $key => $client){
                $enterpriseClient = new EnterpriseClient();
                $enterpriseClient->setPhone((string) rand(55555555, 66666666));
                $enterpriseClient->setMunicipality($this->findMunicipality($manager));
                $enterpriseClient->setEmail('empresa_'.$key.'@gmail.com');
                $enterpriseClient->setStreet('direccion de la calle de la empresa');
                $enterpriseClient->setCorporateEntity($this->findEntity($manager));
                $enterpriseClient->setRepresentative($this->findRepresentative($manager, $representatives[$key+2]));

                $manager->persist($enterpriseClient);
        }
        $manager->flush();
    }

    private function findRepresentative(ObjectManager $manager, $name): ?Representative
    {
        return $manager->getRepository(Representative::class)->findOneBy(['name' => $name]);
    }

    private function findMunicipality(ObjectManager $manager): ?Municipality
    {
        return $manager->getRepository(Municipality::class)->findOneBy(['name' => 'Playa']);
    }

    private function findEntity(ObjectManager $manager): ?CorporateEntity
    {
        $corporateEntities = ['Entidad corporativa 1', 'Entidad corporativa 2', 'Entidad corporativa 3'];
        return $manager->getRepository(CorporateEntity::class)->findOneBy(['name' => $corporateEntities[rand(0,2)]]);
    }

    public function getDependencies(): array
    {
        return [
            RepresentativeFixtures::class,
            ProvinceFixtures::class,
            CorporateEntityFixtures::class
        ];
    }

    public static function getGroups(): array
    {
        return ['default'];
    }
}
