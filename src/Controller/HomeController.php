<?php

namespace App\Controller;

use App\Entity\Building;
use App\Entity\Client;
use App\Entity\Project;
use App\Entity\ProjectUrbanRegulation;
use App\Entity\Role;
use App\Entity\SubSystem;
use App\Service\Building\BuildingValuationService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    #[IsGranted(Role::IS_AUTHENTICATED)]
    public function index(
        BuildingValuationService $buildingValuationService,
        ChartBuilderInterface $chartBuilder,
        EntityManagerInterface $entityManager,
    ): Response {
        $lastThree = $entityManager->getRepository(Project::class)->lastThree();
        $amount = 0;
        $allProjects = $entityManager->getRepository(Project::class)->findAll();
        foreach ($allProjects as $project) {
            $amount += $project->getPrice();
        }

        $newData = $this->subsystems($entityManager);

        list($max, $min) = $this->minMax($entityManager, $buildingValuationService);
        assert($max instanceof Building);
        assert($min instanceof Building);
        $finance = $this->finance($entityManager);

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'last_three' => $lastThree,
            'amount' => $amount,
            'project_amount' => count($allProjects),
            'urban_regulations' => $entityManager->getRepository(ProjectUrbanRegulation::class)->usedUrbanRegulations(),
            'buildings' => $entityManager->getRepository(Building::class)->inSystems(),
            'finance' => $finance,
            'subsystems' => count($newData),
            'min' => $buildingValuationService->getResultIte($min),
            'max' => $buildingValuationService->getResultIte($max),
            'chart1' => $this->chart1($chartBuilder, $finance),
            'chart2' => $this->chart2($chartBuilder, $lastThree),
            'chart3' => $this->chart3($chartBuilder, $finance),
            'chart4' => $this->chart4($chartBuilder, $entityManager->getRepository(Project::class)->countByState()),
            'chart5' => $this->chart5($chartBuilder, $entityManager->getRepository(Client::class)->countClients()),
        ]);
    }

    #[Route('/ping', name: 'app_ping')]
    public function ping(LoggerInterface $logger): Response
    {
        $logger->info('Ping desde NeutralinoJS');

        return new Response('OK');
    }

    /**
     * @return array<mixed>
     */
    public function finance(EntityManagerInterface $entityManager): array
    {
        $newData = [];

        $approvedValue = 0;
        $estimatedValue = 0;
        $estimatedAdjustValue = 0;
        $constructionAssembly = 0;
        $constructionRealValue = 0;
        $buildings = $entityManager->getRepository(Building::class)->findAll();
        foreach ($buildings as $building) {
            $approvedValue += (int) $building->getTotalApprovedValue();
            $estimatedValue += $building->getPrice();
            $estimatedAdjustValue += $building->getEstimatedAdjustValue();
            $constructionAssembly += $building->getConstructionAssembly();
            $constructionRealValue += $building->getConstructionRealValue();
        }

        $newData['approvedValue'] = $approvedValue;
        $newData['estimatedValue'] = $estimatedValue;
        $newData['estimatedAdjustValue'] = $estimatedAdjustValue;
        $newData['constructionAssembly'] = $constructionAssembly;
        $newData['constructionRealValue'] = $constructionRealValue;

        return $newData;
    }

    /**
     * @param array<mixed> $finance
     */
    public function chart1(ChartBuilderInterface $chartBuilder, array $finance): Chart
    {
        /** @var float $approvedValue */
        $approvedValue = $finance['approvedValue'];
        /** @var float $estimatedValue */
        $estimatedValue = $finance['estimatedValue'];

        $chart = $chartBuilder->createChart(Chart::TYPE_BAR);

        $chart->setData([
            'labels' => [''],
            'datasets' => [
                [
                    'label' => number_format($approvedValue * 100 / $estimatedValue, 2).'% aprobado',
                    'backgroundColor' => 'rgb(255, 99, 132)',
                    'borderColor' => 'rgb(255, 99, 132)',
                    'data' => [$approvedValue / 100],
                ],
            ],
        ]);

        $chart->setOptions([
            'indexAxis' => 'y',
            'elements' => [
                'bar' => [
                    'borderWidth' => 2,
                ],
            ],
            'responsive' => true,
            'scales' => [
                'x' => [
                    'suggestedMin' => 0,
                    'suggestedMax' => ($estimatedValue / 100),
                ],
            ],
        ]);

        return $chart;
    }

    public function chart2(ChartBuilderInterface $chartBuilder, mixed $lastThree): Chart
    {
        $chart = $chartBuilder->createChart(Chart::TYPE_PIE);

        $chart->setData([
            'labels' => array_map(function ($item) {
                assert($item instanceof Project);

                return $item->getName();
            }, (array) $lastThree),
            'datasets' => [
                [
                    'label' => [''],
                    'data' => array_map(function ($item) {
                        assert($item instanceof Project);

                        return (float) $item->getPrice() * 10;
                    }, (array) $lastThree),
                ],
            ],
        ]);

        $chart->setOptions([
            'responsive' => true,
        ]);

        return $chart;
    }

    /**
     * @param array<mixed> $finance
     */
    public function chart3(ChartBuilderInterface $chartBuilder, array $finance): Chart
    {
        $chart = $chartBuilder->createChart(Chart::TYPE_BAR);
        $chart->setData([
            'labels' => [''],
            'datasets' => [
                [
                    'label' => 'Valor aprobado',
                    'data' => ['9000000'],
                ],
                [
                    'label' => 'Valor estimado',
                    'data' => [
                        $finance['estimatedValue'],
                    ],
                ],
                [
                    'label' => 'Presupuesto estiamdo',
                    'data' => [
                        $finance['estimatedAdjustValue'],
                    ],
                ],
                [
                    'label' => 'Presupusto detallado',
                    'data' => [
                        $finance['constructionAssembly'],
                    ],
                ],
                [
                    'label' => 'Valor real',
                    'data' => [
                        $finance['constructionRealValue'],
                    ],
                ],
            ],
        ]);

        $chart->setOptions([
            'indexAxis' => 'y',
            'responsive' => true,
            'maintainAspectRatio' => false,
            'barPercentage' => 0.7,
            'categoryPercentage' => 1,
            'scales' => [
                'x' => [
                    'suggestedMin' => 0,
                ],
            ],
            'elements' => [
                'bar' => [
                    'borderWidth' => 2,
                    'borderRadius' => 1,
                    'borderSkipped' => false,
                ],
            ],
            //            'plugins' => [
            //                'tooltip' => [
            //                    'callbacks' => [
            //                        'label' => function ($context) {
            //                            return '$' . $context;
            //                        },
            //                    ],
            //                ],
            //            ]
        ]);

        return $chart;
    }

    public function chart4(ChartBuilderInterface $chartBuilder, mixed $projectsByState): Chart
    {
        $chart = $chartBuilder->createChart(Chart::TYPE_DOUGHNUT);

        $chart->setData([
            'labels' => array_map(function ($item) {
                return match ($item) {
                    'stopped' => 'Detenido',
                    'canceled' => 'Cancelado',
                    'initiated' => 'Iniciado',
                    'urban_regulation' => 'Regulación urbana',
                    'design' => 'Diseño',
                    'registered' => 'Registrado',
                    default => 'No definido',
                };
            }, array_keys((array) $projectsByState)),
            'datasets' => [
                [
                    'label' => [''],
                    'data' => array_values((array) $projectsByState),
                ],
            ],
        ]);

        $chart->setOptions([
            'responsive' => true,
        ]);

        return $chart;
    }

    public function chart5(ChartBuilderInterface $chartBuilder, mixed $clients): Chart
    {
        $chart = $chartBuilder->createChart(Chart::TYPE_BAR);

        $chart->setData([
            'labels' => array_map(function ($item) {
                return match ($item) {
                    'individual_client' => 'Naturales',
                    'representative' => 'Representante',
                    'enterprise_client' => 'Empresarial',
                    default => 'No definido',
                };
            }, array_keys((array) $clients)),
            'datasets' => [
                [
                    'label' => ['Clientes'],
                    'data' => array_values((array) $clients),
                ],
            ],
        ]);

        $chart->setOptions([
            'responsive' => true,
        ]);

        return $chart;
    }

    /**
     * @return array<mixed>
     */
    public function minMax(EntityManagerInterface $entityManager, BuildingValuationService $buildingValuationService): array
    {
        $data = $entityManager->getRepository(Building::class)->getIteReferences('', null, null);
        $data = iterator_to_array($data);

        $maxReduce = function ($carry, $item) use ($buildingValuationService): Building {
            assert($carry instanceof Building);
            assert($item instanceof Building);
            $actualValue = $buildingValuationService->getResultIte($carry);
            $itemValue = $buildingValuationService->getResultIte($item);

            return ($actualValue > $itemValue) ? $carry : $item;
        };
        $max = array_reduce($data, $maxReduce, $data[0]);

        $minReduce = function ($carry, $item) use ($buildingValuationService): Building {
            assert($carry instanceof Building);
            assert($item instanceof Building);
            $actualValue = $buildingValuationService->getResultIte($carry);
            $itemValue = $buildingValuationService->getResultIte($item);

            return ($actualValue < $itemValue) ? $carry : $item;
        };
        $min = array_reduce($data, $minReduce, $data[0]);

        return [$max, $min];
    }

    /**
     * @return array<mixed>
     */
    public function subsystems(EntityManagerInterface $entityManager): array
    {
        $data = $entityManager->getRepository(SubSystem::class)->getIteReferences('', null, null);
        $data = iterator_to_array($data);
        $newData = [];
        for ($i = 0; $i < count($data); ++$i) {
            /** @var SubSystem $subsystem */
            $subsystem = $data[$i];
            if ($subsystem->getPrice() > 0) {
                $newData[] = $subsystem;
            }
        }

        return $newData;
    }
}
