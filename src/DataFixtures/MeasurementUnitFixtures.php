<?php

namespace App\DataFixtures;

use App\Entity\MeasurementUnit;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class MeasurementUnitFixtures extends Fixture implements FixtureGroupInterface
{
    public function load(ObjectManager $manager): void
    {
        $units = [
            'Metro' => 'm',
            'Metro cuadrado' => 'm²',
            'Metro cúbico' => 'm³',
            'Volumen' => 'v',
            'Texto' => 'texto',
            'Porciento' => '%',
            'Numérico' => '#',
            'Dolar por metro cuadrado' => 'USD/m²',
            'Dolar por metro lineal' => 'USD/ml',
            'Dolar por tonelada' => 'USD/tn',
            'Unidad' => 'u',
        ];
        foreach ($units as $name => $code) {
            $unit = $manager->getRepository(MeasurementUnit::class)->findOneBy(['code' => $code]);
            if (is_null($unit)) {
                $unit = new MeasurementUnit();
                $unit->setName($name);
                $unit->setCode($code);
                $manager->persist($unit);
            }
        }

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['default'];
    }

    public function getOrder(): int
    {
        return -1;
    }
}
