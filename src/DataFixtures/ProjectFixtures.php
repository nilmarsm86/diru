<?php

namespace App\DataFixtures;

use App\Entity\Building;
use App\Entity\Client;
use App\Entity\Constructor;
use App\Entity\Contract;
use App\Entity\Draftsman;
use App\Entity\DraftsmanProject;
use App\Entity\EnterpriseClient;
use App\Entity\Enums\ProjectState;
use App\Entity\Enums\ProjectType;
use App\Entity\IndividualClient;
use App\Entity\Investment;
use App\Entity\Project;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ProjectFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $projects = ['Proyect1', 'Proyect2', 'Proyect3',];
        foreach ($projects as $project) {
            $projectEntity = $manager->getRepository(Project::class)->findOneBy(['name' => $project]);
            if (is_null($projectEntity)) {
                $projectEntity = new Project();
                $projectEntity->setName($project);
                $projectEntity->setType(ProjectType::Parcel);//enum
//                $projectEntity->setState(ProjectState::Registered);//enum
//                $projectEntity->setHasOccupiedArea(false);

                if ($project === 'Proyect1') {
                    $investment = $this->findInvestment($manager, 'Inversion1');
                    $investment->setName($investment->getName() . ' ' . $project);
                    $projectEntity->setInvestment($investment);
                    $projectEntity->setClient($this->findClient($manager, true));
                    $projectEntity->setContract($this->findContract($manager, 'abc123'));

                    //esto debe ser automatizado
//                    $draftsmanProject = new DraftsmanProject();
//                    $draftsmanProject->setProject($projectEntity);
//                    $draftsmanProject->setDraftsman($this->findDraftsman($manager, 'Draftsman'));
//                    $draftsmanProject->setStartedAt(new \DateTimeImmutable());

                    $projectEntity->addDraftsman($this->findDraftsman($manager, 'Draftsman'));

                    $projectEntity->addBuilding($this->findBuilding($manager, 'Obra1'));
                }

                if ($project === 'Proyect2') {
                    $investment = $this->findInvestment($manager, 'Inversion2');
                    $investment->setName($investment->getName() . ' ' . $project);
                    $projectEntity->setInvestment($investment);
                    $projectEntity->setClient($this->findClient($manager, false));

                    $projectEntity->addBuilding($this->findBuilding($manager, 'Obra2'));
                }

                if ($project === 'Proyect3') {
                    $investment = $this->findInvestment($manager, 'Inversion3');
                    $investment->setName($investment->getName() . ' ' . $project);
                    $projectEntity->setInvestment($investment);
                    $projectEntity->setClient($this->findClient($manager, true));
                    $projectEntity->setContract($this->findContract($manager, 'qaz753'));

                    $projectEntity->addBuilding($this->findBuilding($manager, 'Obra3'));
                }

                $manager->persist($projectEntity);
            }
        }

        $manager->flush();
    }

    private function findInvestment(ObjectManager $manager, string $investment): ?Investment
    {
        return $manager->getRepository(Investment::class)->findOneBy(['name' => $investment]);
    }

    private function findClient(ObjectManager $manager, bool $enterprise = true): ?Client
    {
        if ($enterprise) {
            $clients = $manager->getRepository(EnterpriseClient::class)->findAll();
        } else {
            $clients = $manager->getRepository(IndividualClient::class)->findAll();
        }
        return $clients[0];
    }

    private function findContract(ObjectManager $manager, string $contract): ?Contract
    {
        return $manager->getRepository(Contract::class)->findOneBy(['code' => $contract]);
    }

    private function findDraftsman(ObjectManager $manager, string $name): ?Draftsman
    {
        return $manager->getRepository(Draftsman::class)->findOneBy(['name' => $name]);
    }

    private function findBuilding(ObjectManager $manager, string $building): ?Building
    {
        return $manager->getRepository(Building::class)->findOneBy(['name' => $building]);
    }

    public function getDependencies(): array
    {
        return [
            InvestmentFixtures::class,
            EnterpriseClientFixtures::class,
            IndividualClientFixtures::class,
            ContractFixtures::class,
            UserFixtures::class,
            BuildingFixtures::class
        ];
    }
}
