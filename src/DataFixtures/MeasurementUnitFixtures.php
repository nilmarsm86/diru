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
        $units = ['m', 'm2', 'm3', 'v', 'texto', '%', '#', 'USD/m2', 'USD/ml', 'USD/tn'];
        foreach ($units as $code) {
            $unit = $manager->getRepository(MeasurementUnit::class)->findOneBy(['code' => $code]);
            if (is_null($unit)) {
                $unit = new MeasurementUnit();
                if ('m' === $code) {
                    $unit->setName('Metro');
                }
                if ('m2' === $code) {
                    $unit->setName('Metro cuadrado');
                }
                if ('m3' === $code) {
                    $unit->setName('Metro cúbico');
                }
                if ('v' === $code) {
                    $unit->setName('Volumne');
                }
                if ('texto' === $code) {
                    $unit->setName('Texto');
                }
                if ('%' === $code) {
                    $unit->setName('Porciento');
                }
                if ('#' === $code) {
                    $unit->setName('Numérico');
                }
                if ('USD/m2' === $code) {
                    $unit->setName('Dolar por metro cuadrado');
                }
                if ('USD/ml' === $code) {
                    $unit->setName('Dolar por metro lineal ?');
                }
                if ('USD/tn' === $code) {
                    $unit->setName('Dolar por tonelada');
                }

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
