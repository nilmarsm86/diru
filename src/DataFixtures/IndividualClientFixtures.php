<?php

namespace App\DataFixtures;

use App\Entity\IndividualClient;
use App\Entity\Municipality;
use App\Entity\Person;
use App\Entity\Representative;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class IndividualClientFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $naturalPersons = ['Person 1', 'Person 2'];
        foreach ($naturalPersons as $naturalPerson){
                $individualClient = new IndividualClient();
                $individualClient->setPerson($this->findPerson($manager, $naturalPerson));
                $individualClient->setStreet('direccion de la calle');
                $individualClient->setMunicipality($this->findMunicipality($manager));
                $individualClient->setEmail($naturalPerson.'@gmail.com');
                $individualClient->setPhone(rand(55555555, 66666666));
                if($naturalPerson === end($naturalPersons)){
                    $individualClient->setRepresentative($this->findRepresentative($manager, $naturalPersons[0]));
                }

                $manager->persist($individualClient);
        }
        $manager->flush();
    }

    private function findPerson(ObjectManager $manager, $name): ?Person
    {
        return $manager->getRepository(Person::class)->findOneBy(['name' => $name]);
    }

    private function findRepresentative(ObjectManager $manager, $name): ?Person
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
            ProvinceFixtures::class
        ];
    }
}
