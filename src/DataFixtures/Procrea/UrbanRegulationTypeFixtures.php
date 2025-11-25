<?php

namespace App\DataFixtures\Procrea;

use App\Entity\LocationZone;
use App\Entity\UrbanRegulationType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Organism;

class UrbanRegulationTypeFixtures extends Fixture implements FixtureGroupInterface
{
    const TYPES = [
        'COS',
        'Alturas',
        'Disposición de la edificación en la parcela',
        'Alineación de las edificaciones',
        'Tipo y elementos de fachada principal',
        'Usos y funciones (clasificación de subsistemas)'
    ];

    public function load(ObjectManager $manager): void
    {
        $types = self::TYPES;
        foreach ($types as $type){
            $urbanRegulationType = $manager->getRepository(UrbanRegulationType::class)->findOneBy(['name' => $type]);
            if(is_null($urbanRegulationType)){
                $urbanRegulationTypeEntity = new UrbanRegulationType();
                $urbanRegulationTypeEntity->setName($type);

                $manager->persist($urbanRegulationTypeEntity);
            }
        }

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['procrea'];
    }
}
