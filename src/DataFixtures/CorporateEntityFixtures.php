<?php

namespace App\DataFixtures;

use App\Entity\CorporateEntity;
use App\Entity\Enums\CorporateEntityType;
use App\Entity\Municipality;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Organism;

class CorporateEntityFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $corporateEntities = ['Entidad corporativa 1', 'Entidad corporativa 2', 'Entidad corporativa 3'];
        foreach ($corporateEntities as $corporateEntity){
            $corporate = $manager->getRepository(CorporateEntity::class)->findOneBy(['name' => $corporateEntity]);
            if(is_null($corporate)){
                $corporate = new CorporateEntity();
                $corporate->setName($corporateEntity);
                $corporate->setCode(strtoupper(substr(base64_encode($corporateEntity), 0, 5)));
                $corporate->setNit(strtolower(base64_encode($corporateEntity)));
                //buscar municipio por nombre y agregarlo
                $corporate->setMunicipality($this->findMunicipality($manager));
                //buscar organismo por nombre y agregarlo
                $corporate->setOrganism($this->findOrganism($manager));
                if($corporateEntity === 'Entidad corporativa 1'){
                    $corporate->setType(CorporateEntityType::Client);
                }

                if($corporateEntity === 'Entidad corporativa 2'){
                    $corporate->setType(CorporateEntityType::Constructor);
                }

                if($corporateEntity === 'Entidad corporativa 3'){
                    $corporate->setType(CorporateEntityType::ClientAndConstructor);
                }

                $manager->persist($corporate);
            }
        }

        $manager->flush();
    }

    private function findMunicipality(ObjectManager $manager): ?Municipality
    {
        return $manager->getRepository(Municipality::class)->findOneBy(['name' => 'Arroyo Naranjo']);
    }

    private function findOrganism(ObjectManager $manager): ?Organism
    {
        return $manager->getRepository(Organism::class)->findOneBy(['name' => 'ONAT']);
    }

    public function getDependencies(): array
    {
        return [
            OrganismFixtures::class,
            ProvinceFixtures::class
        ];
    }
}
