<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Organism;

class OrganismFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $organisms = ['ONAT', 'CDR'];
        foreach ($organisms as $organism){
            $organismEntity = $manager->getRepository(Organism::class)->findOneBy(['name' => $organism]);
            if(is_null($organismEntity)){
                $organismEntity = new Organism();
                $organismEntity->setName($organism);
                $manager->persist($organismEntity);
            }
        }

        $manager->flush();
    }
}
