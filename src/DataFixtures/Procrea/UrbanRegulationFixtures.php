<?php

namespace App\DataFixtures\Procrea;

use App\Entity\UrbanRegulation;
use App\Entity\UrbanRegulationType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class UrbanRegulationFixtures extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $datas = [
            'ALTURAS' => [
                [
                    'code' => 'abc123',
                    'description' => 'Máx. ocupación en parcela (COS)',
                    'data' => 50,
                    'um' => '%',
                    'observation' => 'COMPARATIVA CON OBRA',
                    'legal_reference' => '',
                ],
            ],
            'DISPOSICIÓN DE LA EDIFICACIÓN EN LA PARCELA' => [
                [
                    'code' => 'abc123',
                    'description' => 'Abiertas',
                    'data' => ['Permitido', 'No permitido'],
                    'um' => 'texto',
                    'observation' => 'NO COMPARATIVA-VISUAL-JS',
                    'legal_reference' => '',
                ],
            ],
            'ALINEACIÓN DE LAS EDIFICACIONES' => [
                [
                    'code' => 'abc123',
                    'description' => 'Franja de jardín',
                    'data' => 5,
                    'um' => 'm',
                    'observation' => 'COMPARATIVA CON AREA OCUPADA/AREA LIBRE',
                    'legal_reference' => '',
                ],
            ],
            'TIPO Y ELMENTOS DE FACHADA PRINCIPAL' => [
                [
                    'code' => 'abc123',
                    'description' => 'Portales',
                    'data' => ['Obligatorio', 'Preferente', 'Permisible', 'Restringido', 'Prohibido', 'Obligatorio*', 'Preferente*', 'Permisible*', 'Restringido*', 'Prohibido*'],
                    'um' => 'texto',
                    'observation' => 'COMPARATIVA CON LOCAL (PORTAL)',
                    'legal_reference' => '',
                ],
            ],
            'USOS Y FUNCIONES (CLASIFICACIÓN DE SUBSISTEMAS)' => [
                [
                    'code' => 'abc123',
                    'description' => 'Residencial',
                    'data' => ['Obligatorio', 'Preferente', 'Permisible', 'Restringido', 'Prohibido', 'Obligatorio*', 'Preferente*', 'Permisible*', 'Restringido*', 'Prohibido*'],
                    'um' => 'texto',
                    'observation' => 'COMPARATIVA CON SUBSISTEMA',
                    'legal_reference' => '',
                ],
            ],
        ];

        foreach ($datas as $type => $data) {
            $urbanRegulationEntityType = $manager->getRepository(UrbanRegulationType::class)->findOneBy(['name' => $type]);
            if ($urbanRegulationEntityType) {
                foreach ($data as $regulation) {
                    $urbanRegulation = $manager->getRepository(UrbanRegulation::class)->findOneBy(['description' => $regulation['description']]);
                    if (is_null($urbanRegulation)) {
                        $value = is_array($regulation['data']) ? $regulation['data'][0] : $regulation['data'];

                        $urbanRegulationEntity = new UrbanRegulation();
                        $urbanRegulationEntity->setCode(rand(111111, 999999));
                        $urbanRegulationEntity->setType($urbanRegulationEntityType);
                        $urbanRegulationEntity->setComment($regulation['observation']);
                        $urbanRegulationEntity->setData($value);
                        $urbanRegulationEntity->setDescription($regulation['description']);
                        $urbanRegulationEntity->setMeasurementUnit($regulation['um']);

                        $manager->persist($urbanRegulationEntity);
                    }
                }
            }
        }

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['procrea'];
    }

    public function getDependencies(): array
    {
        return [
            UrbanRegulationTypeFixtures::class
        ];
    }
}
