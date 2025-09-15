<?php

namespace App\DataFixtures\Procrea;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Organism;

class OrganismFixtures extends Fixture implements FixtureGroupInterface
{
    public function load(ObjectManager $manager): void
    {
        $organisms = [
                'Empresa Comercial Caracol S.A',
                'Banco de Crédito y Comercio, Oficina Central',
                'Unidad Municipal Aseguramiento y Apoyo a la Dirección General de Salud Diez de Octubre',
                'Sociedad Mercantil Destellos Servicios Tecnológicos',
                'Empresa Producción y Comercialización de Piensos y Logística Avícola',
                'Empresa del Papel CUBAPEL',
                'Direccion General de Salud de Aseguramiento y Apoyo, Lisa, La Habana',
                'Sociedad Mercantil Industrias NEXUS S.A',
                'Empresa Contratista General de Obras de Varadero, ARCOS'
            ];
        foreach ($organisms as $organism) {
            $organismEntity = $manager->getRepository(Organism::class)->findOneBy(['name' => $organism]);
            if (is_null($organismEntity)) {
                $organismEntity = new Organism();
                $organismEntity->setName($organism);
                $manager->persist($organismEntity);
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            \App\DataFixtures\OrganismFixtures::class
        ];
    }

    public static function getGroups(): array
    {
        return ['procrea'];
    }
}
