<?php

namespace App\DataFixtures;

use App\Entity\Constructor;
use App\Entity\Contract;
use App\Entity\Enums\ConstructiveActionType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ConstructiveActionFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $contracts = [
            'Cambio de uso' => ConstructiveActionType::NoModifier,
            'Mantenimiento' => ConstructiveActionType::NoModifier,
            'Conservacion' => ConstructiveActionType::NoModifier,
        ];
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
}
