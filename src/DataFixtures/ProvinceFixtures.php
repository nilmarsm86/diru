<?php

namespace App\DataFixtures;

use App\Entity\Municipality;
use App\Entity\Province;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProvinceFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $provinces = [/*'Sin provincia', */'Pinar del Río', 'La Habana', 'Artemisa', 'Mayabeque', 'Matanzas', 'Cienfuegos', 'Villa Clara', 'Sancti Spíritus', 'Ciego de Ávila', 'Camagüey', 'Las Tunas', 'Granma', 'Holguín', 'Santiago de Cuba', 'Guantánamo', 'Isla de la Juventud'];
        foreach ($provinces  as $provinceName){
            $province = $manager->getRepository(Province::class)->findOneBy(['name' => $provinceName]);
            if(is_null($province)){
                $result = match ($provinceName) {
//                    'Sin provincia' => $this->addProvinceMunicipality($manager, $provinceName, ['Sin municipio']),
                    'Pinar del Río' => $this->addProvinceMunicipality($manager, $provinceName, ['Candelaria']),
                    'La Habana' => $this->addProvinceMunicipality($manager, $provinceName, ['Arroyo Naranjo', '10 de Octubre', 'Playa', 'Cerro', 'La Lisa', 'Boyeros', 'Habana del Este', 'Marianao', 'Plaza', 'Habana Vieja', 'Centro Habana', 'Guanabacoa']),
                    'Artemisa' => $this->addProvinceMunicipality($manager, $provinceName, ['Artemisa']),
                    'Mayabeque' => $this->addProvinceMunicipality($manager, $provinceName, ['San Antonio']),
                    'Matanzas' => $this->addProvinceMunicipality($manager, $provinceName, ['Varadero', 'Cárdenas']),
                    'Cienfuegos' => $this->addProvinceMunicipality($manager, $provinceName, ['Cienfuegos']),
                    'Villa Clara' => $this->addProvinceMunicipality($manager, $provinceName, ['Santa Clara']),
                    'Sancti Spíritus' => $this->addProvinceMunicipality($manager, $provinceName, ['Sancti Spíritus']),
                    'Ciego de Ávila' => $this->addProvinceMunicipality($manager, $provinceName, ['Ciego de Ávila']),
                    'Camagüey' => $this->addProvinceMunicipality($manager, $provinceName, ['Camagüey']),
                    'Las Tunas' => $this->addProvinceMunicipality($manager, $provinceName, ['Las Tunas']),
                    'Granma' => $this->addProvinceMunicipality($manager, $provinceName, ['Bayamo']),
                    'Holguín' => $this->addProvinceMunicipality($manager, $provinceName, ['Holguín']),
                    'Santiago de Cuba' => $this->addProvinceMunicipality($manager, $provinceName, ['Santiago de Cuba']),
                    'Guantánamo' => $this->addProvinceMunicipality($manager, $provinceName, ['Baracoa']),
                    'Isla de la Juventud' => $this->addProvinceMunicipality($manager, $provinceName, ['Isla de la Juventud']),
                    default => false,
                };

                if(!$result){
                    $manager->persist((new Province())->setName($provinceName));
                }
            }
        }

        $manager->flush();
    }

    /**
     * @param ObjectManager $manager
     * @param string $provinceName
     * @param array $municipalities
     * @return bool
     */
    public function addProvinceMunicipality(ObjectManager $manager, string $provinceName, array $municipalities): bool
    {
        $province = (new Province())->setName($provinceName);
        foreach ($municipalities as $municipality){
            $province->addMunicipality((new Municipality())->setName($municipality));
        }

        $manager->persist($province);

        return true;
    }

}
