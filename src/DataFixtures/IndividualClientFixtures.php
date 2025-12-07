<?php

namespace App\DataFixtures;

use App\Entity\IndividualClient;
use App\Entity\Municipality;
use App\Entity\Person;
use App\Entity\Representative;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class IndividualClientFixtures extends Fixture implements DependentFixtureInterface, FixtureGroupInterface
{
    public function load(ObjectManager $manager): void
    {
        $clients = ['Cliente Individual 1', 'Cliente Individual 2'];
        $persons = ['Person 1', 'Person 2', 'Person 3', 'Person 4'];
        $representatives = ['Representante 1', 'Representante 2', 'Representante 3', 'Representante 4'];
        foreach ($clients as $key => $client){
                $individualClient = new IndividualClient();
                $individualClient->setPerson($this->findPerson($manager, $persons[$key]));
                $individualClient->setStreet('direccion de la calle');
                $individualClient->setMunicipality($this->findMunicipality($manager));
                $individualClient->setEmail($client.'@gmail.com');
                $individualClient->setPhone((string) rand(55555555, 66666666));
                if($client === end($clients)){
                    $individualClient->setRepresentative($this->findRepresentative($manager, $representatives[$key]));
                }

                $manager->persist($individualClient);
        }
        $manager->flush();
    }

    private function findPerson(ObjectManager $manager, $name): ?Person
    {
        return $manager->getRepository(Person::class)->findOneBy(['name' => $name]);
    }

    private function findRepresentative(ObjectManager $manager, $name): ?Representative
    {
        return $manager->getRepository(Representative::class)->findOneBy(['name' => $name]);
    }

    private function findMunicipality(ObjectManager $manager): ?Municipality
    {
        return $manager->getRepository(Municipality::class)->findOneBy(['name' => 'Arroyo Naranjo']);
    }

    public function getDependencies(): array
    {
        return [
            PersonFixtures::class,
            RepresentativeFixtures::class,
            ProvinceFixtures::class
        ];
    }

    public static function getGroups(): array
    {
        return ['default'];
    }
}
