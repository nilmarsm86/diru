<?php

namespace App\Controller;

use App\DTO\Paginator;
use App\Repository\BuildingRepository;
use App\Repository\SubSystemRepository;
use App\Service\Building\BuildingValuationService;
use App\Service\CrudActionService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\RouterInterface;

#[Route('/historical/ite')]
final class HistoricalIteController extends AbstractController
{
    #[Route('/subsystem', name: 'app_historical_ite_subsystem', methods: ['GET'])]
    public function subsystem(Request $request, RouterInterface $router, CrudActionService $crudActionService, SubSystemRepository $subSystemRepository): Response
    {
        /** @var array{string, int, int} $result */
        $result = $crudActionService->getManageQuerys($request);
        list($filter, $amountPerPage, $pageNumber) = $result;

        $data = $subSystemRepository->getIteReferences($filter, $amountPerPage, $pageNumber);

        $paginator = new Paginator($data, $amountPerPage, $pageNumber);
        if ($paginator->isFromGreaterThanTotal()) {
            return $paginator->greatherThanTotal($request, $router, $pageNumber);
        }

        $template = ($request->isXmlHttpRequest()) ? '_subsystems.html.twig' : 'index.html.twig';

        return $this->render("historical_ite/$template", [
            'filter' => $filter,
            'paginator' => $paginator,
            'title' => 'ITE subsistemas',
            'template' => 'historical_ite/_subsystems.html.twig',
        ]);
    }

    #[Route('/building', name: 'app_historical_ite_building', methods: ['GET'])]
    public function building(Request $request, RouterInterface $router, CrudActionService $crudActionService, BuildingRepository $buildingRepository, BuildingValuationService $buildingValuationService): Response
    {
        /** @var array{string, int, int} $result */
        $result = $crudActionService->getManageQuerys($request);
        list($filter, $amountPerPage, $pageNumber) = $result;

        $data = $buildingRepository->getIteReferences($filter, $amountPerPage, $pageNumber);

        $paginator = new Paginator($data, $amountPerPage, $pageNumber);
        if ($paginator->isFromGreaterThanTotal()) {
            return $paginator->greatherThanTotal($request, $router, $pageNumber);
        }

        $template = ($request->isXmlHttpRequest()) ? '_building.html.twig' : 'index.html.twig';

        return $this->render("historical_ite/$template", [
            'filter' => $filter,
            'paginator' => $paginator,
            'title' => 'ITE obras',
            'template' => 'historical_ite/_building.html.twig',
            'buildingValuationService' => $buildingValuationService,
        ]);
    }
}
