<?php

namespace App\DataFixtures;

use App\Entity\ConstructiveSystem;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ConstructiveSystemFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $systems = ['Ninguno', 'Mixto', 'Tradicional', 'Panelería ligera', 'Prefabricado'];
        foreach ($systems as $name) {
            $constructiveSystem = $manager->getRepository(ConstructiveSystem::class)->findOneBy(['name' => $name]);
            if (is_null($constructiveSystem)) {
                $constructiveSystem = new ConstructiveSystem();
                $constructiveSystem->setName($name);

                $manager->persist($constructiveSystem);
            }
        }

        $manager->flush();
    }
}
