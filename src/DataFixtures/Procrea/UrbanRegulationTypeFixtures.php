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
    public function load(ObjectManager $manager): void
    {
        $types = [
            'ALTURAS',
            'DISPOSICIÓN DE LA EDIFICACIÓN EN LA PARCELA',
            'ALINEACIÓN DE LAS EDIFICACIONES',
            'TIPO Y ELMENTOS DE FACHADA PRINCIPAL',
            'USOS Y FUNCIONES (CLASIFICACIÓN DE SUBSISTEMAS)'
        ];
        foreach ($types as $type){
            $urbanRegulationType = $manager->getRepository(UrbanRegulationType::class)->findOneBy(['name' => $type]);
            if(is_null($urbanRegulationType)){
                $urbanRegulationTypeEntity = new UrbanRegulationType();
                $urbanRegulationTypeEntity->setName(ucfirst(strtolower($type)));

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
