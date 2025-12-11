<?php

namespace App\DataFixtures;

use App\Entity\Currency;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class CurrencyFixtures extends Fixture implements FixtureGroupInterface
{
    public function load(ObjectManager $manager): void
    {
        $currencies = ['CUP', 'USD'];
        foreach ($currencies as $currencyCode) {
            $currency = $manager->getRepository(Currency::class)->findOneBy(['code' => $currencyCode]);
            if (is_null($currency)) {
                $currency = new Currency();
                if ('CUP' === $currencyCode) {
                    $currency->setName('Peso Cubano');
                }

                if ('USD' === $currencyCode) {
                    $currency->setName('Dolar EEUU');
                }

                $currency->setCode($currencyCode);
                $manager->persist($currency);
            }
        }

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['default'];
    }
}
