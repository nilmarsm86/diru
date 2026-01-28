<?php

namespace App\Controller;

use App\DTO\Paginator;
use App\Entity\Building;
use App\Entity\BuildingRevision;
use App\Repository\BuildingRevisionRepository;
use App\Service\CrudActionService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\RouterInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

#[Route('/building/revision')]
final class BuildingRevisionController extends AbstractController
{
    #[Route('/{building}', name: 'app_building_revision_index', methods: ['GET'])]
    public function index(Request $request, RouterInterface $router, BuildingRevisionRepository $buildingRevisionRepository, Building $building): Response
    {
        $filter = $request->query->get('filter', '');
        $amountPerPage = (int) $request->query->get('amount', '10');
        $pageNumber = (int) $request->query->get('page', '1');

        $data = $buildingRevisionRepository->findRevisions($building, $filter, $amountPerPage, $pageNumber);

        $paginator = new Paginator($data, $amountPerPage, $pageNumber);
        if ($paginator->isFromGreaterThanTotal()) {
            return $paginator->greatherThanTotal($request, $router, $pageNumber);
        }

        $template = ($request->isXmlHttpRequest()) ? '_list.html.twig' : 'index.html.twig';

        return $this->render("building_revision/$template", [
            'filter' => $filter,
            'paginator' => $paginator,
            'building' => $building,
            'title' => 'Revisiones',
        ]);
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    #[Route('/new/{building}', name: 'app_building_revision_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CrudActionService $crudActionService, ?Building $building = null): Response
    {
        $buildingRevision = new BuildingRevision();

        return $crudActionService->formLiveComponentAction($request, $buildingRevision, 'building_revision', [
            'title' => 'Nueva Revisión',
            'building' => $building,
        ]);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/show/{id}/{building}', name: 'app_building_revision_show', methods: ['GET'])]
    public function show(Request $request, BuildingRevision $buildingRevision, CrudActionService $crudActionService, Building $building): Response
    {
        return $crudActionService->showAction($request, $buildingRevision, 'building_revision', 'building_revision', 'Detalles de la Revisión', [
            'building' => $building,
            'title' => 'Detalle de la revisión',
        ]);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/{id}/edit/{building}', name: 'app_building_revision_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, BuildingRevision $buildingRevision, CrudActionService $crudActionService, Building $building): Response
    {
        return $crudActionService->formLiveComponentAction($request, $buildingRevision, 'building_revision', [
            'title' => 'Editar Revisión',
            'building' => $building,
        ]);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/{id}/{building}', name: 'app_building_revision_delete', methods: ['POST'])]
    public function delete(Request $request, BuildingRevision $buildingRevision, BuildingRevisionRepository $buildingRevisionRepository, CrudActionService $crudActionService, Building $building): Response
    {
        $successMsg = 'Se ha eliminado la revisión.';
        $response = $crudActionService->deleteAction($request, $buildingRevisionRepository, $buildingRevision, $successMsg, 'app_building_edit', ['id' => $building->getId()]);
        if ($response instanceof RedirectResponse) {
            $this->addFlash('success', $successMsg);

            return $response;
        }

        return $response;
    }
}
