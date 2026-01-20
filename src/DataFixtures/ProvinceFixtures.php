<?php

namespace App\DataFixtures;

use App\Entity\Municipality;
use App\Entity\Province;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class ProvinceFixtures extends Fixture implements FixtureGroupInterface
{
    public function load(ObjectManager $manager): void
    {
        $provincesWithMunicipalities = [
            'Sin provincia' => ['Sin municipio'],
            'Pinar del Río' => ['Candelaria'],
            'La Habana' => ['Arroyo Naranjo', '10 de Octubre', 'Playa', 'Cerro', 'La Lisa', 'Boyeros', 'Habana del Este', 'Marianao', 'Plaza', 'Habana Vieja', 'Centro Habana', 'Guanabacoa'],
            'Artemisa' => ['Artemisa'],
            'Mayabeque' => ['San Antonio'],
            'Matanzas' => ['Varadero', 'Cárdenas'],
            'Cienfuegos' => ['Cienfuegos'],
            'Villa Clara' => ['Santa Clara'],
            'Sancti Spíritus' => ['Sancti Spíritus'],
            'Ciego de Ávila' => ['Ciego de Ávila'],
            'Camagüey' => ['Camagüey'],
            'Las Tunas' => ['Las Tunas'],
            'Granma' => ['Bayamo'],
            'Holguín' => ['Holguín'],
            'Santiago de Cuba' => ['Santiago de Cuba'],
            'Guantánamo' => ['Baracoa'],
            'Isla de la Juventud' => ['Isla de la juventud'],
        ];

        foreach ($provincesWithMunicipalities as $provinceName => $municipalities) {
            $province = $manager->getRepository(Province::class)->findOneBy(['name' => $provinceName]);

            if (null === $province) {
                $this->addProvinceMunicipality($manager, $provinceName, $municipalities);
            }
        }

        $manager->flush();
    }

    /**
     * @param array<string> $municipalities
     */
    public function addProvinceMunicipality(ObjectManager $manager, string $provinceName, array $municipalities): bool
    {
        $province = (new Province())->setName($provinceName);
        foreach ($municipalities as $municipality) {
            $province->addMunicipality((new Municipality())->setName($municipality));
        }

        $manager->persist($province);

        return true;
    }

    public static function getGroups(): array
    {
        return ['default'];
    }
}
