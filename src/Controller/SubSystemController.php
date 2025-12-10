<?php

namespace App\Controller;

use App\DTO\Paginator;
use App\Entity\ConstructiveAction;
use App\Entity\Floor;
use App\Entity\SubSystem;
use App\Form\SubSystemType;
use App\Repository\ConstructiveActionRepository;
use App\Repository\LocalRepository;
use App\Repository\SubSystemRepository;
use App\Service\CrudActionService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\RouterInterface;

#[Route('/sub/system')]
final class SubSystemController extends AbstractController
{
    #[Route('/{floor}/{reply}', name: 'app_sub_system_index', requirements: ['floor' => '\d+'], methods: ['GET'])]
    public function index(Request $request, RouterInterface $router, CrudActionService $crudActionService, SubSystemRepository $subSystemRepository, Floor $floor, bool $reply = false): Response
    {
        /** @var array{string, int, int} $result */
        $result = $crudActionService->getManageQuerys($request);
        list($filter, $amountPerPage, $pageNumber) = $result;

        $data = $subSystemRepository->findSubsystemsFloor($floor, $filter, $amountPerPage, $pageNumber, $reply);

        $paginator = new Paginator($data, $amountPerPage, $pageNumber);
        if ($paginator->isFromGreaterThanTotal()) {
            return $paginator->greatherThanTotal($request, $router, $pageNumber);
        }

        $template = ($request->isXmlHttpRequest()) ? '_list.html.twig' : 'index.html.twig';

        return $this->render("sub_system/$template", [
            'filter' => $filter,
            'paginator' => $paginator,
            'floor' => $floor,
            'reply' => $reply
        ]);
    }

    #[Route('/new/{floor}/{reply}', name: 'app_sub_system_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CrudActionService $crudActionService, Floor $floor, bool $reply = false): Response
    {
        $subSystem = new SubSystem();
        return $crudActionService->formLiveComponentAction($request, $subSystem, 'sub_system', [
            'title' => 'Nuevo Subsistema',
            'floor' => $floor,
            'reply' => $reply,
        ]);
    }

    #[Route('/{id}', name: 'app_sub_system_show', methods: ['GET'])]
    public function show(Request $request, SubSystem $subSystem, CrudActionService $crudActionService): Response
    {
        return $crudActionService->showAction($request, $subSystem, 'sub_system', 'subSystem', 'Detalles del subsistema');
    }

    #[Route('/{id}/edit/{floor}/{reply}', name: 'app_sub_system_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, SubSystem $subSystem, CrudActionService $crudActionService, Floor $floor, bool $reply = false): Response
    {
        return $crudActionService->formLiveComponentAction($request, $subSystem, 'sub_system', [
            'title' => 'Editar Subsistema',
            'floor' => $floor,
            'reply' => $reply,
        ]);
    }

    #[Route('/{id}', name: 'app_sub_system_delete', methods: ['POST'])]
    public function delete(Request $request, SubSystem $subSystem, SubSystemRepository $subSystemRepository, CrudActionService $crudActionService, Floor $floor): Response
    {
        $successMsg = 'Se ha eliminado el subsistema.';
        $response = $crudActionService->deleteAction($request, $subSystemRepository, $subSystem, $successMsg, 'app_sub_system_index', [
            'floor' => $floor->getId()
        ]);
        if($response instanceof RedirectResponse){
            $this->addFlash('success', $successMsg);
            return $response;
        }

        return $response;
    }

    #[Route('/{id}/report/local', name: 'app_sub_system_report_local', methods: ['GET'])]
    public function reportLocal(SubSystem $subSystem, ConstructiveActionRepository $constructiveActionRepository): Response
    {
        $constructiveActionStatus = $subSystem->getAmountConstructiveAction();
        $constructiveActionPrice = $subSystem->getPriceByConstructiveAction();
        $constructiveActionMeter = $subSystem->getMeterByConstructiveAction();
        $constructiveActions = $constructiveActionRepository->findAll();
        $ca = [];

        foreach ($constructiveActions as $constructiveAction) {
            if (!array_key_exists($constructiveAction->getName(), $constructiveActionMeter)) {
                $ca[$constructiveAction->getName()] = [
                    'status' => 0,
                    'price' => 0,
                    'meter' => 0,
                ];
            } else {
                $ca[$constructiveAction->getName()] = [
                    'status' => $constructiveActionStatus[$constructiveAction->getName()],
                    'price' => $constructiveActionPrice[$constructiveAction->getName()],
                    'meter' => $constructiveActionMeter[$constructiveAction->getName()],
                ];
            }
        }

        return $this->render("sub_system/report.html.twig", [
            'local_status' => $subSystem->getAmountTechnicalStatus(),
            'meter_status' => $subSystem->getAmountMeterTechnicalStatus(),
            'constructive_action' => $ca,
            'title' => 'Estado técnico de los locales del subsistema',
            'sub_system' => $subSystem
        ]);
    }
}
