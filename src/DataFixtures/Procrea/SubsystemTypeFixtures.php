<?php

namespace App\DataFixtures\Procrea;

use App\Entity\Enums\SubsystemFunctionalClassification;
use App\Entity\SubsystemType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class SubsystemTypeFixtures extends Fixture implements FixtureGroupInterface
{
    public function load(ObjectManager $manager): void
    {
//        $types = ['Sin tipo', 'Residencial', 'Alojamiento', 'Administrativo', 'Comercio', 'Grastronomía',
//            'Servicios de ciudad', 'Servicios básicos de salud', 'Servicios básicos de educación',
//            'Servicios básicos socioculturales-recreativos', 'Servicios básicos deportivos', 'Servicios básicos otros',
//            'Almacenes/talleres pequeños', 'Recojida de desechos solidos', 'Especiales', 'Producción', 'Religioso',
//            'Agropecuario', 'Parqueos construidos'];
        $types = [
            '0' => [
                'Vivienda rural unifamiliar', 'Vivienda rural multifamiliar', 'Vivienda urbana unifamiliar social',
                'Vivienda urbana unifamiliar de mediano estandar', 'Vivienda urbana unifamiliar de alto estandar',
                'Vivienda urbana multifamiliar social', 'Vivienda urbana multifamiliar de mediano estandar',
                'Vivienda urbana multifamiliar de alto estandar'
            ],
            '1' => [
                'Salud', 'Educación', 'Comerciales', 'Gastronómicos', 'Socioculturales y Recreativos', 'Deportivos', 'Administrativos',
                'Otros servicios urbanos'
            ],
            '2' => [],
            '3' => [],
        ];

        foreach ($types as $classification => $typeNames) {
            foreach ($typeNames as $typeName){
                $type = $manager->getRepository(SubsystemType::class)->findOneBy(['name' => $typeName]);
                if (is_null($type)) {
                    $subsystemType = new SubsystemType();
                    $subsystemType->setName($typeName);
                    $subsystemType->setClassification(SubsystemFunctionalClassification::from($classification));
                    $manager->persist($subsystemType);
                }
            }

        }

        $manager->flush();
    }

//    /**
//     * @param ObjectManager $manager
//     * @param string $provinceName
//     * @param array $municipalities
//     * @return bool
//     */
//    public function addProvinceMunicipality(ObjectManager $manager, string $provinceName, array $municipalities): bool
//    {
//        $province = (new Province())->setName($provinceName);
//        foreach ($municipalities as $municipality){
//            $province->addMunicipality((new Municipality())->setName($municipality));
//        }
//
//        $manager->persist($province);
//
//        return true;
//    }

    public static function getGroups(): array
    {
        return ['procrea'];
    }

}
