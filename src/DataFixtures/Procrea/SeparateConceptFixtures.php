<?php

namespace App\DataFixtures\Procrea;

use App\Entity\Enums\SeparateConceptType;
use App\Entity\SeparateConcept;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class SeparateConceptFixtures extends Fixture implements FixtureGroupInterface
{
    public function load(ObjectManager $manager): void
    {
        $concepts = [
            [
                'type' => SeparateConceptType::Branch,
                'number' => 1,
                'formula' => null,
                'parent' => null,
                'name' => 'Materias primas y materiales',
            ],
            [
                'type' => SeparateConceptType::Leaf,
                'number' => 1.1,
                'formula' => null,
                'parent' => 1,
                'name' => 'Materiales aportados por el Constructor Estimado',
            ],
        ];
        foreach ($concepts as $concept) {
            $conceptEntity = $manager->getRepository(SeparateConcept::class)->findOneBy(['name' => $concept['name']]);
            if (is_null($conceptEntity)) {
                $conceptEntity = new SeparateConcept();
                $conceptEntity->setName($concept['name']);
                $conceptEntity->setType($concept['type']);
                $conceptEntity->setNumber($concept['number']);
                $conceptEntity->setFormula($concept['formula']);
                if(!is_null($concept['parent'])){
                    $parentConcept = $manager->getRepository(SeparateConcept::class)->findOneBy(['number' => $concept['parent']]);
                    if(!is_null($parentConcept)){
                        $conceptEntity->setParent($parentConcept);
                    }
                }

                $manager->persist($conceptEntity);
//                if(is_null($concept['parent'])){
                    $manager->flush();
//                }
            }
        }

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['procrea'];
    }
}
