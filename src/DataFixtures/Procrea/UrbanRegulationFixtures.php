<?php

namespace App\DataFixtures\Procrea;

use App\Entity\Enums\UrbanRegulationStructure;
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
            UrbanRegulationTypeFixtures::TYPES[0] => [
                [
                    'code' => 'abc123',
                    'description' => 'Máx. ocupación en parcela (COS)',
                    'data' => 50,
                    'um' => '%',
                    'observation' => 'COMPARATIVA CON OBRA',
                    'legal_reference' => '',
                    'structure' => UrbanRegulationStructure::Building,
                ],
            ],
            UrbanRegulationTypeFixtures::TYPES[1] => [
                [
                    'code' => 'abc123',
                    'description' => 'No. máximo de niveles',
                    'data' => 4,
                    'um' => 'Plantas',
                    'observation' => 'COMPARATIVA CON OBRA',
                    'legal_reference' => '',
                    'structure' => UrbanRegulationStructure::Building,
                ],
                [
                    'code' => 'abc123',
                    'description' => 'No. mínimo de niveles',
                    'data' => 2,
                    'um' => 'Plantas',
                    'observation' => 'COMPARATIVA CON OBRA',
                    'legal_reference' => '',
                    'structure' => UrbanRegulationStructure::Building,
                ],
                [
                    'code' => 'abc123',
                    'description' => 'Máxima altura total',
                    'data' => 15,
                    'um' => 'm',
                    'observation' => 'COMPARATIVA CON OBRA',
                    'legal_reference' => '',
                    'structure' => UrbanRegulationStructure::Building,
                ],
                [
                    'code' => 'abc123',
                    'description' => 'Puntal mínimo planta baja',
                    'data' => 5,
                    'um' => 'm',
                    'observation' => 'COMPARATIVA CON PLANTA',
                    'legal_reference' => '',
                    'structure' => UrbanRegulationStructure::Floor,
                ],
                [
                    'code' => 'abc123',
                    'description' => 'Puntal mínimo general',
                    'data' => 2.5,
                    'um' => 'm',
                    'observation' => 'COMPARATIVA CON PLANTA',
                    'legal_reference' => '',
                    'structure' => UrbanRegulationStructure::Floor,
                ]
            ],
            UrbanRegulationTypeFixtures::TYPES[2] => [
                [
                    'code' => 'abc123',
                    'description' => 'Abiertas',
                    'data' => ['Permitido', 'No permitido'],
                    'um' => 'texto',
                    'observation' => 'NO COMPARATIVA-VISUAL-JS',
                    'legal_reference' => '',
                    'structure' => UrbanRegulationStructure::Building,
                ],
                [
                    'code' => 'abc123',
                    'description' => 'Compactas',
                    'data' => ['Permitido', 'No permitido'],
                    'um' => 'texto',
                    'observation' => 'NO COMPARATIVA-VISUAL-JS',
                    'legal_reference' => '',
                    'structure' => UrbanRegulationStructure::Building,
                ],
                [
                    'code' => 'abc123',
                    'description' => 'Semi-compactas',
                    'data' => ['Permitido', 'No permitido'],
                    'um' => 'texto',
                    'observation' => 'NO COMPARATIVA-VISUAL-JS',
                    'legal_reference' => '',
                    'structure' => UrbanRegulationStructure::Building,
                ]
            ],
            UrbanRegulationTypeFixtures::TYPES[3] => [
                [
                    'code' => 'abc123',
                    'description' => 'Franja de jardín',
                    'data' => 5,
                    'um' => 'm',
                    'observation' => 'COMPARATIVA CON AREA OCUPADA/AREA LIBRE',
                    'legal_reference' => '',
                    'structure' => UrbanRegulationStructure::Local,
                ],
                [
                    'code' => 'abc123',
                    'description' => 'Portal y Medioportal',
                    'data' => 2.5,
                    'um' => 'm',
                    'observation' => 'COMPARATIVO CON LOCAL "PORTAL"',
                    'legal_reference' => '',
                    'structure' => UrbanRegulationStructure::Local,
                ],
                [
                    'code' => 'abc123',
                    'description' => 'Portal corrido',
                    'data' => 2,
                    'um' => 'm',
                    'observation' => 'COMPARATIVO CON LOCAL "PORTAL"',
                    'legal_reference' => '',
                    'structure' => UrbanRegulationStructure::Local,
                ],
                [
                    'code' => 'abc123',
                    'description' => 'Pasillo lateral',
                    'data' => 1,
                    'um' => 'm',
                    'observation' => 'COMPARATIVA CON AREA OCUPADA/AREA LIBRE',
                    'legal_reference' => '',
                    'structure' => UrbanRegulationStructure::Local,
                ],
                [
                    'code' => 'abc123',
                    'description' => 'Pasillo de fondo mínimo',
                    'data' => 1,
                    'um' => 'm',
                    'observation' => 'COMPARATIVA CON AREA OCUPADA/AREA LIBRE',
                    'legal_reference' => '',
                    'structure' => UrbanRegulationStructure::Local,
                ],
                [
                    'code' => 'abc123',
                    'description' => 'Retranqueos',
                    'data' => 1.5,
                    'um' => 'm',
                    'observation' => 'NO COMPARATIVA-VISUAL-JS',
                    'legal_reference' => '',
                    'structure' => UrbanRegulationStructure::Local,
                ],
                [
                    'code' => 'abc123',
                    'description' => 'Patios Interiores',
                    'data' => 2,
                    'um' => 'm',
                    'observation' => 'COMPARATIVA CON AREA OCUPADA/AREA LIBRE',
                    'legal_reference' => '',
                    'structure' => UrbanRegulationStructure::Local,
                ],
                [
                    'code' => 'abc123',
                    'description' => 'Cercado',
                    'data' => 1,
                    'um' => 'm',
                    'observation' => 'NO COMPARATIVA-VISUAL-JS',
                    'legal_reference' => '',
                    'structure' => UrbanRegulationStructure::Local,
                ]
            ],
            UrbanRegulationTypeFixtures::TYPES[4] => [
                [
                    'code' => 'abc123',
                    'description' => 'Portales',
                    'data' => ['Obligatorio', 'Preferente', 'Permisible', 'Restringido', 'Prohibido', 'Obligatorio*', 'Preferente*', 'Permisible*', 'Restringido*', 'Prohibido*'],
                    'um' => 'texto',
                    'observation' => 'COMPARATIVA CON LOCAL (PORTAL)',
                    'legal_reference' => '',
                    'structure' => UrbanRegulationStructure::Local,
                ],
                [
                    'code' => 'abc123',
                    'description' => 'Cercado',
                    'data' => ['Obligatorio', 'Preferente', 'Permisible', 'Restringido', 'Prohibido', 'Obligatorio*', 'Preferente*', 'Permisible*', 'Restringido*', 'Prohibido*'],
                    'um' => 'texto',
                    'observation' => 'NO COMPARATIVA-VISUAL-JS',
                    'legal_reference' => '',
                    'structure' => UrbanRegulationStructure::Local,
                ],
                [
                    'code' => 'abc123',
                    'description' => 'Portal de uso público',
                    'data' => ['Obligatorio', 'Preferente', 'Permisible', 'Restringido', 'Prohibido', 'Obligatorio*', 'Preferente*', 'Permisible*', 'Restringido*', 'Prohibido*'],
                    'um' => 'texto',
                    'observation' => 'COMPARATIVA CON LOCAL (PORTAL PÚBLICO)',
                    'legal_reference' => '',
                    'structure' => UrbanRegulationStructure::Local,
                ],
                [
                    'code' => 'abc123',
                    'description' => 'Vistas y luces',
                    'data' => ['Obligatorio', 'Preferente', 'Permisible', 'Restringido', 'Prohibido', 'Obligatorio*', 'Preferente*', 'Permisible*', 'Restringido*', 'Prohibido*'],
                    'um' => 'texto',
                    'observation' => 'NO COMPARATIVA-VISUAL-JS',
                    'legal_reference' => '',
                    'structure' => UrbanRegulationStructure::Local,
                ],
                [
                    'code' => 'abc123',
                    'description' => 'Salientes',
                    'data' => ['Obligatorio', 'Preferente', 'Permisible', 'Restringido', 'Prohibido', 'Obligatorio*', 'Preferente*', 'Permisible*', 'Restringido*', 'Prohibido*'],
                    'um' => 'texto',
                    'observation' => 'NO COMPARATIVA-VISUAL-JS',
                    'legal_reference' => '',
                    'structure' => UrbanRegulationStructure::Local,
                ],
                [
                    'code' => 'abc123',
                    'description' => 'Balcones, loggias y terrazas',
                    'data' => ['Obligatorio', 'Preferente', 'Permisible', 'Restringido', 'Prohibido', 'Obligatorio*', 'Preferente*', 'Permisible*', 'Restringido*', 'Prohibido*'],
                    'um' => 'texto',
                    'observation' => 'COMPARATIVA CON LOCAL (BALCONES, LOGGIAS, TERRAZAS)',
                    'legal_reference' => '',
                    'structure' => UrbanRegulationStructure::Local,
                ]
            ],
            UrbanRegulationTypeFixtures::TYPES[5] => [
                [
                    'code' => 'abc123',
                    'description' => 'Residencial',
                    'data' => ['Obligatorio', 'Preferente', 'Permisible', 'Restringido', 'Prohibido', 'Obligatorio*', 'Preferente*', 'Permisible*', 'Restringido*', 'Prohibido*'],
                    'um' => 'texto',
                    'observation' => 'COMPARATIVA CON SUBSISTEMA',
                    'legal_reference' => '',
                    'structure' => UrbanRegulationStructure::SubSystem,
                ],
                [
                    'code' => 'abc123',
                    'description' => 'Espacios público-verde',
                    'data' => ['Obligatorio', 'Preferente', 'Permisible', 'Restringido', 'Prohibido', 'Obligatorio*', 'Preferente*', 'Permisible*', 'Restringido*', 'Prohibido*'],
                    'um' => 'texto',
                    'observation' => 'COMPARATIVA CON SUBSISTEMA',
                    'legal_reference' => '',
                    'structure' => UrbanRegulationStructure::SubSystem,
                ],
                [
                    'code' => 'abc123',
                    'description' => 'Alojamiento',
                    'data' => ['Obligatorio', 'Preferente', 'Permisible', 'Restringido', 'Prohibido', 'Obligatorio*', 'Preferente*', 'Permisible*', 'Restringido*', 'Prohibido*'],
                    'um' => 'texto',
                    'observation' => 'COMPARATIVA CON SUBSISTEMA',
                    'legal_reference' => '',
                    'structure' => UrbanRegulationStructure::SubSystem,
                ],
                [
                    'code' => 'abc123',
                    'description' => 'Administrativo',
                    'data' => ['Obligatorio', 'Preferente', 'Permisible', 'Restringido', 'Prohibido', 'Obligatorio*', 'Preferente*', 'Permisible*', 'Restringido*', 'Prohibido*'],
                    'um' => 'texto',
                    'observation' => 'COMPARATIVA CON SUBSISTEMA',
                    'legal_reference' => '',
                    'structure' => UrbanRegulationStructure::SubSystem,
                ],
                [
                    'code' => 'abc123',
                    'description' => 'Comercio',
                    'data' => ['Obligatorio', 'Preferente', 'Permisible', 'Restringido', 'Prohibido', 'Obligatorio*', 'Preferente*', 'Permisible*', 'Restringido*', 'Prohibido*'],
                    'um' => 'texto',
                    'observation' => 'COMPARATIVA CON SUBSISTEMA',
                    'legal_reference' => '',
                    'structure' => UrbanRegulationStructure::SubSystem,
                ],
                [
                    'code' => 'abc123',
                    'description' => 'Gastronomía',
                    'data' => ['Obligatorio', 'Preferente', 'Permisible', 'Restringido', 'Prohibido', 'Obligatorio*', 'Preferente*', 'Permisible*', 'Restringido*', 'Prohibido*'],
                    'um' => 'texto',
                    'observation' => 'COMPARATIVA CON SUBSISTEMA',
                    'legal_reference' => '',
                    'structure' => UrbanRegulationStructure::SubSystem,
                ],
                [
                    'code' => 'abc123',
                    'description' => 'Servicios de Ciudad',
                    'data' => ['Obligatorio', 'Preferente', 'Permisible', 'Restringido', 'Prohibido', 'Obligatorio*', 'Preferente*', 'Permisible*', 'Restringido*', 'Prohibido*'],
                    'um' => 'texto',
                    'observation' => 'COMPARATIVA CON SUBSISTEMA',
                    'legal_reference' => '',
                    'structure' => UrbanRegulationStructure::SubSystem,
                ],
                [
                    'code' => 'abc123',
                    'description' => 'Servicios Básicos Salud',
                    'data' => ['Obligatorio', 'Preferente', 'Permisible', 'Restringido', 'Prohibido', 'Obligatorio*', 'Preferente*', 'Permisible*', 'Restringido*', 'Prohibido*'],
                    'um' => 'texto',
                    'observation' => 'COMPARATIVA CON SUBSISTEMA',
                    'legal_reference' => '',
                    'structure' => UrbanRegulationStructure::SubSystem,
                ],
                [
                    'code' => 'abc123',
                    'description' => 'Servicios Básicos Educación',
                    'data' => ['Obligatorio', 'Preferente', 'Permisible', 'Restringido', 'Prohibido', 'Obligatorio*', 'Preferente*', 'Permisible*', 'Restringido*', 'Prohibido*'],
                    'um' => 'texto',
                    'observation' => 'COMPARATIVA CON SUBSISTEMA',
                    'legal_reference' => '',
                    'structure' => UrbanRegulationStructure::SubSystem,
                ],
                [
                    'code' => 'abc123',
                    'description' => 'Servicios Básicos Socioculturales-Recreativos',
                    'data' => ['Obligatorio', 'Preferente', 'Permisible', 'Restringido', 'Prohibido', 'Obligatorio*', 'Preferente*', 'Permisible*', 'Restringido*', 'Prohibido*'],
                    'um' => 'texto',
                    'observation' => 'COMPARATIVA CON SUBSISTEMA',
                    'legal_reference' => '',
                    'structure' => UrbanRegulationStructure::SubSystem,
                ],
                [
                    'code' => 'abc123',
                    'description' => 'Servicios Básicos Deportivos',
                    'data' => ['Obligatorio', 'Preferente', 'Permisible', 'Restringido', 'Prohibido', 'Obligatorio*', 'Preferente*', 'Permisible*', 'Restringido*', 'Prohibido*'],
                    'um' => 'texto',
                    'observation' => 'COMPARATIVA CON SUBSISTEMA',
                    'legal_reference' => '',
                    'structure' => UrbanRegulationStructure::SubSystem,
                ],
                [
                    'code' => 'abc123',
                    'description' => 'Servicios Básicos Administrativos',
                    'data' => ['Obligatorio', 'Preferente', 'Permisible', 'Restringido', 'Prohibido', 'Obligatorio*', 'Preferente*', 'Permisible*', 'Restringido*', 'Prohibido*'],
                    'um' => 'texto',
                    'observation' => 'COMPARATIVA CON SUBSISTEMA',
                    'legal_reference' => '',
                    'structure' => UrbanRegulationStructure::SubSystem,
                ],
                [
                    'code' => 'abc123',
                    'description' => 'Servicios Básicos Otros',
                    'data' => ['Obligatorio', 'Preferente', 'Permisible', 'Restringido', 'Prohibido', 'Obligatorio*', 'Preferente*', 'Permisible*', 'Restringido*', 'Prohibido*'],
                    'um' => 'texto',
                    'observation' => 'COMPARATIVA CON SUBSISTEMA',
                    'legal_reference' => '',
                    'structure' => UrbanRegulationStructure::SubSystem,
                ],
                [
                    'code' => 'abc123',
                    'description' => 'Almacenes/talleres pequeños',
                    'data' => ['Obligatorio', 'Preferente', 'Permisible', 'Restringido', 'Prohibido', 'Obligatorio*', 'Preferente*', 'Permisible*', 'Restringido*', 'Prohibido*'],
                    'um' => 'texto',
                    'observation' => 'COMPARATIVA CON SUBSISTEMA',
                    'legal_reference' => '',
                    'structure' => UrbanRegulationStructure::SubSystem,
                ],
                [
                    'code' => 'abc123',
                    'description' => 'Recogido de Desechos Solidos',
                    'data' => ['Obligatorio', 'Preferente', 'Permisible', 'Restringido', 'Prohibido', 'Obligatorio*', 'Preferente*', 'Permisible*', 'Restringido*', 'Prohibido*'],
                    'um' => 'texto',
                    'observation' => 'COMPARATIVA CON SUBSISTEMA',
                    'legal_reference' => '',
                    'structure' => UrbanRegulationStructure::SubSystem,
                ],
                [
                    'code' => 'abc123',
                    'description' => 'Especiales',
                    'data' => ['Obligatorio', 'Preferente', 'Permisible', 'Restringido', 'Prohibido', 'Obligatorio*', 'Preferente*', 'Permisible*', 'Restringido*', 'Prohibido*'],
                    'um' => 'texto',
                    'observation' => 'COMPARATIVA CON SUBSISTEMA',
                    'legal_reference' => '',
                    'structure' => UrbanRegulationStructure::SubSystem,
                ],
                [
                    'code' => 'abc123',
                    'description' => 'Producción',
                    'data' => ['Obligatorio', 'Preferente', 'Permisible', 'Restringido', 'Prohibido', 'Obligatorio*', 'Preferente*', 'Permisible*', 'Restringido*', 'Prohibido*'],
                    'um' => 'texto',
                    'observation' => 'COMPARATIVA CON SUBSISTEMA',
                    'legal_reference' => '',
                    'structure' => UrbanRegulationStructure::SubSystem,
                ],
                [
                    'code' => 'abc123',
                    'description' => 'Religioso',
                    'data' => ['Obligatorio', 'Preferente', 'Permisible', 'Restringido', 'Prohibido', 'Obligatorio*', 'Preferente*', 'Permisible*', 'Restringido*', 'Prohibido*'],
                    'um' => 'texto',
                    'observation' => 'COMPARATIVA CON SUBSISTEMA',
                    'legal_reference' => '',
                    'structure' => UrbanRegulationStructure::SubSystem,
                ],
                [
                    'code' => 'abc123',
                    'description' => 'Agropecuario',
                    'data' => ['Obligatorio', 'Preferente', 'Permisible', 'Restringido', 'Prohibido', 'Obligatorio*', 'Preferente*', 'Permisible*', 'Restringido*', 'Prohibido*'],
                    'um' => 'texto',
                    'observation' => 'COMPARATIVA CON SUBSISTEMA',
                    'legal_reference' => '',
                    'structure' => UrbanRegulationStructure::SubSystem,
                ],
                [
                    'code' => 'abc123',
                    'description' => 'Parqueos Construidos',
                    'data' => ['Obligatorio', 'Preferente', 'Permisible', 'Restringido', 'Prohibido', 'Obligatorio*', 'Preferente*', 'Permisible*', 'Restringido*', 'Prohibido*'],
                    'um' => 'texto',
                    'observation' => 'COMPARATIVA CON SUBSISTEMA',
                    'legal_reference' => '',
                    'structure' => UrbanRegulationStructure::SubSystem,
                ]
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
                        $urbanRegulationEntity->setCode((string) rand(111111, 999999));
                        $urbanRegulationEntity->setType($urbanRegulationEntityType);
                        $urbanRegulationEntity->setComment($regulation['observation']);
                        $urbanRegulationEntity->setData($value);
                        $urbanRegulationEntity->setDescription($regulation['description']);
                        $urbanRegulationEntity->setMeasurementUnit($regulation['um']);
                        $urbanRegulationEntity->setStructure($regulation['structure']);

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
