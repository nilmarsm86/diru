<?php

namespace App\DataFixtures\Procrea;

use App\Entity\Organism;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class OrganismFixtures extends Fixture implements FixtureGroupInterface
{
    public function load(ObjectManager $manager): void
    {
        $organisms = [
            'Oficina Nacional de la Administración Tributaria (ONAT)',
            'Organizaciones de Masas (CDR, UJC, FEEM, FEU)',
            'Ministerio de la Construcción (MICONS)',
            'Ministerio de Salud Pública (MINSAP)',
            'Ministerio de Comunicaciones (MICOM)',
            'Proyectos TCP (Trabajador por Cuenta Propia)',
            'Mipymes (Micro, Pequeña y Mediana Empresa)',
            'Ministerio de la Agricultura (MINAGRI)',
            'Ministerio de Comercio Interior (MINCIN)',
            'Ministerio de Educación (MINED)',
            'Ministerio de Ciencia, Tecnología y Medio Ambiente (CITMA)',
            'Ministerio del Transporte (MITRANS)',
            'Ministerio del Comercio Exterior e Inversión Extranjera (MINCEX)',
            'Ministerio de Economía y Planificación (MEP)',
            'Ministerio de Educación Superior (MES)',
            'Instituto Nacional de Actores Económicos No Estatales (INAENE)',
            'Ministerio de Industria (MINDUS)',
            'Ministerio de Cultura (MINCULT)',
            'Instituto Nacional del Deporte y la Recreación (INDER)',
            'Instituto Nacional de Recursos Hidráulicos (INRH)',
            'Instituto Nacional de Ordenamiento Territorial y Urbanismo (INOTU)',
            'Unión Eléctrica Nacional (UNE)',
            'Ministerio de Energía y Minas (MINEM)',
            'Ministerio de Finanzas y Precios (MFP)',
            'Ministerio del Turismo (MINTUR)',
            'Ministerio de Justicia (MINJUS)',
            'Ministerio de Relaciones Exteriores (MINREX)',
            'Ministerio de Trabajo y Seguridad Social (MTSS)',
            'Ministerio de las Fuerzas Armadas (MINFAR)',
            'Ministerio del Interior (MININ)',
            'Oficina Nacional de Estadística e Información (ONEI)',
            'Banco Central de Cuba (BCC)',
            'Instituto Cubano de Radio y Televisión (ICRT)',
            'Gobierno Provincial',
            'Gobierno Municipal',
            'Dirección Provincial',
            'Dirección Municipal',
            'Empresa Extranjera',
        ];

        foreach ($organisms as $organism) {
            $organismEntity = $manager->getRepository(Organism::class)->findOneBy(['name' => $organism]);
            if (is_null($organismEntity)) {
                $organismEntity = new Organism();
                $organismEntity->setName($organism);
                $manager->persist($organismEntity);
            }
        }

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['procrea'];
    }

    public function getOrder(): int
    {
        return 13;
    }
}
