<?php

namespace App\Controller;

use App\DTO\Paginator;
use App\Entity\Floor;
use App\Entity\SubSystem;
use App\Form\SubSystemType;
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
    #[Route('/{floor}', name: 'app_sub_system_index', methods: ['GET'])]
    public function index(Request $request, CrudActionService $crudActionService, SubSystemRepository $subSystemRepository, Floor $floor): Response
    {
        list($filter, $amountPerPage, $pageNumber) = $crudActionService->getManageQuerys($request);

        $data = $subSystemRepository->findSubsystemsFloor($floor, $filter, $amountPerPage, $pageNumber);

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
            'floor' => $floor
        ]);
    }

    #[Route('/new/{floor}', name: 'app_sub_system_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CrudActionService $crudActionService, Floor $floor): Response
    {
        $subSystem = new SubSystem();
        return $crudActionService->formLiveComponentAction($request, $subSystem, 'sub_system', [
            'title' => 'Nuevo Sub Sistema',
            'floor' => $floor
        ]);
    }

    #[Route('/{id}', name: 'app_sub_system_show', methods: ['GET'])]
    public function show(Request $request, SubSystem $subSystem, CrudActionService $crudActionService): Response
    {
        return $crudActionService->showAction($request, $subSystem, 'sub_system', 'subSystem', 'Detalles del sub sistema');
    }

    #[Route('/{id}/edit/{floor}', name: 'app_sub_system_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, SubSystem $subSystem, CrudActionService $crudActionService, Floor $floor): Response
    {
        return $crudActionService->formLiveComponentAction($request, $subSystem, 'sub_system', [
            'title' => 'Editar Sub sistema',
            'floor' => $floor
        ]);
    }

    #[Route('/{id}', name: 'app_sub_system_delete', methods: ['POST'])]
    public function delete(Request $request, SubSystem $subSystem, SubSystemRepository $subSystemRepository, CrudActionService $crudActionService, Floor $floor): Response
    {
        $successMsg = 'Se ha eliminado el sub sistema.';
        return $crudActionService->deleteAction($request, $subSystemRepository, $subSystem, $successMsg, 'app_sub_system_index', [
            'floor' => $floor->getId()
        ]);
    }
}
