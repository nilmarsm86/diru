<?php

namespace App\DataFixtures;

use App\Entity\CorporateEntity;
use App\Entity\EnterpriseClient;
use App\Entity\IndividualClient;
use App\Entity\Municipality;
use App\Entity\Person;
use App\Entity\Representative;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class EnterpriseClientFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $representatives = ['Person 3', 'Person 4'];
        foreach ($representatives as $representative){
                $enterpriseClient = new EnterpriseClient();
                $enterpriseClient->setPhone(rand(55555555, 66666666));
                $enterpriseClient->setMunicipality($this->findMunicipality($manager));
                $enterpriseClient->setEmail('empresa_'.$representative.'@gmail.com');
                $enterpriseClient->setStreet('direccion de la calle de la empresa');
                $enterpriseClient->setCorporateEntity($this->findEntity($manager));
                if($representative === end($representatives)){
                    $enterpriseClient->setRepresentative($this->findRepresentative($manager, $representatives[0]));
                }

                $manager->persist($enterpriseClient);
        }
        $manager->flush();
    }

    private function findRepresentative(ObjectManager $manager, $name): ?Person
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
            PersonFixtures::class,
            ProvinceFixtures::class,
            CorporateEntityFixtures::class
        ];
    }
}
