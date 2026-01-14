<?php

namespace App\DataFixtures;

use App\DataFixtures\Procrea\UrbanRegulationFixtures;
use App\Entity\Building;
use App\Entity\Client;
use App\Entity\Contract;
use App\Entity\Currency;
use App\Entity\Draftsman;
use App\Entity\DraftsmanProject;
use App\Entity\EnterpriseClient;
use App\Entity\Enums\ProjectState;
use App\Entity\Enums\ProjectType;
use App\Entity\IndividualClient;
use App\Entity\Investment;
use App\Entity\Project;
use App\Entity\ProjectUrbanRegulation;
use App\Entity\UrbanRegulation;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ProjectFixtures extends Fixture implements DependentFixtureInterface, FixtureGroupInterface
{
    public function load(ObjectManager $manager): void
    {
        $projects = ['Proyect1', 'Proyect2', 'Proyect3'];
        foreach ($projects as $project) {
            $projectEntity = $manager->getRepository(Project::class)->findOneBy(['name' => $project]);
            if (is_null($projectEntity)) {
                $projectEntity = new Project();
                $projectEntity->setName($project);
                $projectEntity->setType(ProjectType::Parcel); // enum
                //                $projectEntity->setState(ProjectState::Registered);//enum
                //                $projectEntity->setHasOccupiedArea(false);

                if ('Proyect1' === $project) {
                    $this->addInvestment($manager, $project, $projectEntity, 'Inversion1', true, true, 'Obra1');

                    $this->addUrbanRegulation($manager, $projectEntity, 'Máx. ocupación en parcela (COS)', '50');
                    $this->addUrbanRegulation($manager, $projectEntity, 'Puntal mínimo general', '2.5');
                    $this->addUrbanRegulation($manager, $projectEntity, 'Parqueos Construidos', 'Obligatorio');
                }

                if ('Proyect2' === $project) {
                    //                    if ($investment = $this->findInvestment($manager, 'Inversion2')) {
                    //                        $investment->setName($investment->getName().' '.$project);
                    //                        $projectEntity->setInvestment($investment);
                    //                        $projectEntity->setClient($this->findClient($manager, false));
                    //
                    //                        $building = $this->findBuilding($manager, 'Obra2');
                    //                        if (null !== $building) {
                    //                            $projectEntity->addBuilding($building);
                    //                        }
                    //                    }
                    $this->addInvestment($manager, $project, $projectEntity, 'Inversion2', false, false, 'Obra2');

                    $this->addUrbanRegulation($manager, $projectEntity, 'Patios Interiores', '100');
                    $this->addUrbanRegulation($manager, $projectEntity, 'Pasillo lateral', '1');
                    $this->addUrbanRegulation($manager, $projectEntity, 'Portal y Medioportal', '2.5');
                }

                if ('Proyect3' === $project) {
                    //                    if ($investment = $this->findInvestment($manager, 'Inversion3')) {
                    //                        $investment->setName($investment->getName().' '.$project);
                    //                        $projectEntity->setInvestment($investment);
                    //                        $projectEntity->setClient($this->findClient($manager, true));
                    //                        $projectEntity->setContract($this->findContract($manager, 'qaz753'));
                    //
                    //                        $building = $this->findBuilding($manager, 'Obra3');
                    //                        if (null !== $building) {
                    //                            $projectEntity->addBuilding($building);
                    //                        }
                    //                    }
                    $this->addInvestment($manager, $project, $projectEntity, 'Inversion3', true, true, 'Obra3');

                    $this->addUrbanRegulation($manager, $projectEntity, 'Cercado', '1');
                    $this->addUrbanRegulation($manager, $projectEntity, 'Portales', 'Preferente');
                    $this->addUrbanRegulation($manager, $projectEntity, 'Retranqueos', '1.5');
                }

                $projectEntity->setCurrency($this->findCurrency($manager, 'CUP'));

                $manager->persist($projectEntity);
            }
        }

        $manager->flush();
    }

    private function addUrbanRegulation(ObjectManager $manager, Project $project, string $urbanRegulationDescription, string $data): void
    {
        $projectUrbanRegulation = new ProjectUrbanRegulation();
        $projectUrbanRegulation->setProject($project);
        $projectUrbanRegulation->setUrbanRegulation($this->findUrbanRegulation($manager, $urbanRegulationDescription));
        $projectUrbanRegulation->setData($data);
        $projectUrbanRegulation->setReference('Referencia de la regulacion');

        $project->addProjectUrbanRegulation($projectUrbanRegulation);
    }

    private function findCurrency(ObjectManager $manager, string $code): ?Currency
    {
        return $manager->getRepository(Currency::class)->findOneBy(['code' => $code]);
    }

    private function findInvestment(ObjectManager $manager, string $investment): ?Investment
    {
        return $manager->getRepository(Investment::class)->findOneBy(['name' => $investment]);
    }

    private function findClient(ObjectManager $manager, bool $enterprise = true): Client
    {
        $entityClass = ($enterprise) ? EnterpriseClient::class : IndividualClient::class;
        $clients = $manager->getRepository($entityClass)->findAll();

        return $clients[0];
    }

    private function findContract(ObjectManager $manager, string $contract): ?Contract
    {
        return $manager->getRepository(Contract::class)->findOneBy(['code' => $contract]);
    }

    //    private function findDraftsman(ObjectManager $manager, string $name): ?Draftsman
    //    {
    //        return $manager->getRepository(Draftsman::class)->findOneBy(['name' => $name]);
    //    }

    private function findBuilding(ObjectManager $manager, string $building): ?Building
    {
        return $manager->getRepository(Building::class)->findOneBy(['name' => $building]);
    }

    private function findUrbanRegulation(ObjectManager $manager, string $urbanRegulationDescription): ?UrbanRegulation
    {
        return $manager->getRepository(UrbanRegulation::class)->findOneBy(['description' => $urbanRegulationDescription]);
    }

    public function getDependencies(): array
    {
        return [
            InvestmentFixtures::class,
            EnterpriseClientFixtures::class,
            IndividualClientFixtures::class,
            ContractFixtures::class,
            UserFixtures::class,
            BuildingFixtures::class,
            CurrencyFixtures::class,
            UrbanRegulationFixtures::class,
        ];
    }

    public static function getGroups(): array
    {
        return ['default'];
    }

    public function addInvestment(ObjectManager $manager, string $project, Project $projectEntity, string $investmentName, bool $contract, bool $clientEnterprise, string $buildName): void
    {
        $investment = $this->findInvestment($manager, $investmentName);
        if (null !== $investment) {
            $investment->setName($investment->getName().' '.$project);
            $projectEntity->setInvestment($investment);
            $projectEntity->setClient($this->findClient($manager, $clientEnterprise));
            if (true === $contract) {
                $contract = $this->findContract($manager, 'abc123');
                if (null === $contract) {
                    $projectEntity->setContract($contract);
                }
            }

            // esto debe ser automatizado
            //                    $draftsmanProject = new DraftsmanProject();
            //                    $draftsmanProject->setProject($projectEntity);
            //                    $draftsmanProject->setDraftsman($this->findDraftsman($manager, 'Draftsman'));
            //                    $draftsmanProject->setStartedAt(new \DateTimeImmutable());

            //                    $projectEntity->addDraftsman($this->findDraftsman($manager, 'Draftsman'));
            $building = $this->findBuilding($manager, $buildName);
            if (null !== $building) {
                $projectEntity->addBuilding($building);
            }
        }
    }
}
