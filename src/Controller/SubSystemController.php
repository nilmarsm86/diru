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

#[Route('/sub/system')]
final class SubSystemController extends AbstractController
{
    #[Route('/{floor}/{reply}', name: 'app_sub_system_index', methods: ['GET'], requirements: ['floor' => '\d+'])]
    public function index(Request $request, CrudActionService $crudActionService, SubSystemRepository $subSystemRepository, Floor $floor, bool $reply = false): Response
    {
        list($filter, $amountPerPage, $pageNumber) = $crudActionService->getManageQuerys($request);

        $data = $subSystemRepository->findSubsystemsFloor($floor, $filter, $amountPerPage, $pageNumber, $reply);

        $paginator = new Paginator($data, $amountPerPage, $pageNumber);
        if ($paginator->isFromGreaterThanTotal()) {
            $number = ($pageNumber === 1) ?? ($pageNumber - 1);
            return new RedirectResponse($this->generateUrl($request->attributes->get('_route'), [
                ...$request->query->all(),
                'page' => $number
            ]), Response::HTTP_SEE_OTHER);
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
        return $crudActionService->deleteAction($request, $subSystemRepository, $subSystem, $successMsg, 'app_sub_system_index', [
            'floor' => $floor->getId()
        ]);
    }

    #[Route('/{id}/report/local', name: 'app_sub_system_report_local', methods: ['GET'])]
    public function reportLocal(SubSystem $subSystem, ConstructiveActionRepository $constructiveActionRepository): Response
    {
        $constructiveActionStatus = $subSystem->getAmountConstructiveAction();
        $constructiveActionPrice = $subSystem->getPriceByConstructiveAction();
        $constructiveActions = $constructiveActionRepository->findAll();

        foreach ($constructiveActions as $constructiveAction){
            if(!array_key_exists($constructiveAction->getName(), $constructiveActionStatus)){
                $constructiveActionStatus[$constructiveAction->getName()] = 0;
            }

            if(!array_key_exists($constructiveAction->getName(), $constructiveActionPrice)){
                $constructiveActionPrice[$constructiveAction->getName()] = 0;
            }
        }

        return $this->render("sub_system/report.html.twig", [
            'local_status' => $subSystem->getAmountTechnicalStatus(),
            'meter_status' => $subSystem->getAmountMeterTechnicalStatus(),
            'constructive_action_status' => $constructiveActionStatus,
            'constructive_action_price' => $constructiveActionPrice,
            'title' => 'Estado técnico de los locales del subsistema',
            'sub_system' => $subSystem
        ]);
    }
}
