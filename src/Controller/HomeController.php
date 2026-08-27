<?php

namespace App\Controller;

use App\Entity\Role;
use App\Entity\SubSystem;
use App\Repository\BuildingRepository;
use App\Repository\ProjectRepository;
use App\Repository\ProjectUrbanRegulationRepository;
use App\Repository\SubSystemRepository;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    #[IsGranted(Role::IS_AUTHENTICATED)]
    public function index(
        ProjectRepository $projectRepository,
        ProjectUrbanRegulationRepository $projectUrbanRegulationRepository,
        BuildingRepository $buildingRepository,
        SubSystemRepository $subsystemRepository,
    ): Response {
        $lastThree = $projectRepository->lastThree();
        $amount = 0;
        foreach ($projectRepository->findAll() as $project) {
            $amount += $project->getPrice();
        }

        $data = $subsystemRepository->getIteReferences('', null, null);

        $data = iterator_to_array($data);
        $newData = [];
        for ($i = 0; $i < count($data); ++$i) {
            /** @var SubSystem $subsystem */
            $subsystem = $data[$i];
            if ($subsystem->getPrice() > 0) {
                $newData[] = $subsystem;
            }
        }

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'last_three' => $lastThree,
            'amount' => $amount,
            'project_amount' => count($projectRepository->findAll()),
            'urban_regulations' => $projectUrbanRegulationRepository->usedUrbanRegulations(),
            'buildings' => $buildingRepository->inSystems(),
            'finance' => $this->finance($buildingRepository),
            'subsystems' => count($newData),
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
    public function finance(BuildingRepository $buildingRepository): array
    {
        $newData = [];

        $approvedValue = 0;
        $estimatedValue = 0;
        $estimatedAdjustValue = 0;
        $constructionAssembly = 0;
        $constructionRealValue = 0;
        $buildings = $buildingRepository->findAll();
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
}
