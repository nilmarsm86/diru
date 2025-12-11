<?php

namespace App\DataFixtures\Procrea;

use App\Entity\Enums\SubsystemFunctionalClassification;
use App\Entity\SubsystemSubType;
use App\Entity\SubsystemType;
use App\Entity\SubsystemTypeSubsystemSubType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class SubsystemTypeFixtures extends Fixture implements FixtureGroupInterface
{
    public function load(ObjectManager $manager): void
    {
        $viviendaClasificacion = [
            'Estudio (E)',
            'Para 2 personas (D)',
            'Para 3 personas (Dd)',
            'Para 4 personas (2D)', 'Para 4 personas (D2d)',
            'Para 5 personas (2Dd)', 'Para 5 personas (D3d)',
            'Para 6 personas (2D2d)', 'Para 6 personas (D4d)',
            'Para 7 personas (3Dd)', 'Para 7 personas (2D3d)',
            'Para 8 personas (4D)', 'Para 8 personas (3d2D)', 'Para 8 personas (2d4D)',
        ];

        $types = [
            '0' => [
                'Vivienda rural unifamiliar' => $viviendaClasificacion,
                'Vivienda rural multifamiliar' => $viviendaClasificacion,
                'Vivienda urbana unifamiliar social' => $viviendaClasificacion,
                'Vivienda urbana unifamiliar de mediano estandar' => $viviendaClasificacion,
                'Vivienda urbana unifamiliar de alto estandar' => $viviendaClasificacion,
                'Vivienda urbana multifamiliar social' => $viviendaClasificacion,
                'Vivienda urbana multifamiliar de mediano estandar' => $viviendaClasificacion,
                'Vivienda urbana multifamiliar de alto estandar' => $viviendaClasificacion,
            ],
            '1' => [
                'Salud' => ['Farmacia de Turno normal', 'Consultorios Médicos', 'Casa y Clubes de abuelos', 'Policlínicos (Sin Ingresos)', 'Hogar de ancianos pequeño'],
                'Educación' => ['Círculos infantiles', 'Escuelas primaria', 'Escuelas secundarias'],
                'Comerciales' => ['Tienda de  alimentos (Bodegas)', 'Tiendas Temáticas (TRD-Artex)', 'Carnicería-pescadería', 'Supermercado-Mercado agropecuario', 'Minimercados y conjuntos contiguos', 'Tiendas de productos industriales y artesanales (Enseres Menores)', 'Lavandería/tintorería', 'Peluquería', 'Barbería', 'Mini-punto Periódicos y Revistas*', 'Mini-punto de telecomunicaciones', 'Fotografías', 'Reparación de Calzado', 'Taller de reparación de Enseres menores, muebles y cocinas', 'Taller de Costura', 'Taller de reparación de Electrodomésticos'],
                'Gastronómicos' => ['Panadería-dulcería', 'Bar pequeño', 'Restaurante Pequeño', 'Cafetería Priv. de Alimentos Ligeros', 'Cafetería Pirv. de con Comida Rápida', 'Cafetería Estatales'],
                'Socioculturales y Recreativos' => ['Sala de computacion (Joven Club)', 'Sala de video', 'Centro Cultural Polifuncional', 'Biblioteca Comunitaria Pequeña'],
                'Deportivos' => ['Canchas deportivas mixtas', 'Gimnasios', 'Complejo de Piscinas', 'Salas Polivalentes'],
                'Administrativos' => ['Administrativos', 'Oficina de correos', 'Oficina de cobros', 'Bancos pequeños', 'Centro Cívico y Asociativo', 'Cajeros automáticos', 'Servicio de telefonía, correos electrónicos', 'Salas de Navegacion de Internet'],
                'Otros servicios urbanos' => ['Parqueos y Garajes Locales', 'Local de Zona administrativa', 'Punto de Gas', 'Centro de elaboración merienda escolar', 'Circulo de abuelos', 'Puntos para la recogida selectiva de residuos', 'Centro de Higiene', 'Baños públicos', 'Estación taxis', 'Parqueo de camiones'],
            ],
            '2' => [
                'Salud' => ['Farmacia de Turno permanente', 'Hospitales Generales y especializados', 'Estomatología y fisioterapia', 'Hogar de ancianos grande'],
                'Educación' => ['Universidades', 'Escuelas de capacitación', 'Preuniversitario', 'Escuela de idiomas', 'Escuelas técnicas', 'Escuelas especiales', 'Escuelas Normal de maestros', 'Escuelas licenciatura general', 'Escuelas licenciatura tecnológica postgrado', 'Centros de computación'],
                'Comerciales' => [],
                'Gastronómicos' => [],
                'Socioculturales y Recreativos' => [],
                'Deportivos' => [],
                'Administrativos' => [],
                'Turismo' => [],
                'Otros' => [],
            ],
            '3' => [
                'Industria Alimentaria' => [],
                'Industria de la construcción' => [],
                'Talleres industriales' => [],
                'Turismo' => [],
                'Almacenes' => [],
                'Infraestructura' => [],
                'Cientifico-Administrativa' => [],
            ],
        ];

        foreach ($types as $classification => $typeNames) {
            foreach ($typeNames as $typeName => $subs) {
                //                $type = $manager->getRepository(SubsystemType::class)->findOneBy(['name' => $typeName]);
                //                if (is_null($type)) {
                $type = new SubsystemType();
                $type->setName($typeName);
                $type->setClassification(SubsystemFunctionalClassification::from($classification));

                $manager->persist($type);
                $manager->flush();
                //                }

                $this->addSubType($manager, $type, $subs);
            }
        }
    }

    /**
     * @param array<string> $subs
     */
    public function addSubType(ObjectManager $manager, SubsystemType $subsystemType, array $subs): void
    {
        foreach ($subs as $subName) {
            //            $isNew = false;
            $subType = $manager->getRepository(SubsystemSubType::class)->findOneBy(['name' => $subName]);
            if (is_null($subType)) {
                $subType = new SubsystemSubType();
                $subType->setName($subName);
                //                $isNew = true;
                $manager->persist($subType);
                $manager->flush();
            }

            $stsst = new SubsystemTypeSubsystemSubType();
            $stsst->setSubsystemType($subsystemType);
            $stsst->setSubsystemSubType($subType);

            $manager->persist($stsst);
            $manager->flush();
            //            $subsystemType->addSubsystemSubType($subType);
            //            if ($isNew) {
            //                $manager->persist($subsystemType);

            //            }
        }
    }

    public static function getGroups(): array
    {
        return ['procrea'];
    }
}
