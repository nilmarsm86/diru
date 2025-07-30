<?php

namespace App\DataFixtures;

use App\Entity\ConstructiveAction;
use App\Entity\Constructor;
use App\Entity\Contract;
use App\Entity\Enums\ConstructiveActionType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ConstructiveActionFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $actions = [
            'Cambio de uso' => ConstructiveActionType::NoModifier,
            'Mantenimiento' => ConstructiveActionType::NoModifier,
            'Conservación' => ConstructiveActionType::NoModifier,
            'Rehabilitación' => ConstructiveActionType::Modifier,//modificacion de los elementos constructivos sin demoler
            'Demolición' => ConstructiveActionType::Modifier,
            'Escombreo y limpieza' => ConstructiveActionType::NoModifier,
            'Ampliación' => ConstructiveActionType::Modifier,
            'Obra nueva' => ConstructiveActionType::Modifier,
            'Eliminación' => ConstructiveActionType::Modifier,
            'Remodelación' => ConstructiveActionType::Modifier,//implica demolicion de algo
        ];
        foreach ($actions as $name => $type) {
            $constructiveAction = $manager->getRepository(ConstructiveAction::class)->findOneBy(['name' => $name]);
            if (is_null($constructiveAction)) {
                $constructiveAction = new ConstructiveAction();
                $constructiveAction->setName($name);
                $constructiveAction->setType($type);

                $manager->persist($constructiveAction);
            }
        }

        $manager->flush();
    }
}
