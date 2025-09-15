<?php

namespace App\DataFixtures;

use App\Entity\Constructor;
use App\Entity\Contract;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class ContractFixtures extends Fixture implements FixtureGroupInterface
{
    public function load(ObjectManager $manager): void
    {
        $contracts = ['abc123', 'zyx987', 'qaz753'];
        foreach ($contracts as $contract) {
            $contractEntity = $manager->getRepository(Contract::class)->findOneBy(['code' => $contract]);
            if (is_null($contractEntity)) {
                $contractEntity = new Contract();
                $contractEntity->setCode($contract);
                $contractEntity->setYear(2025);

                $manager->persist($contractEntity);
            }
        }

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['default'];
    }
}
