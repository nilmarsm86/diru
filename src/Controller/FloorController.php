<?php

namespace App\Controller;

use App\DTO\Paginator;
use App\Entity\Building;
use App\Entity\Floor;
use App\Repository\FloorRepository;
use App\Service\CrudActionService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

#[Route('/floor')]
final class FloorController extends AbstractController
{
    #[Route('/{building}/{reply}', name: 'app_floor_index', requirements: ['building' => '\d+'], methods: ['GET'])]
    public function index(Request $request, FloorRepository $floorRepository, Building $building, bool $reply = false): Response
    {
        $filter = $request->query->get('filter', '');
        $amountPerPage = $request->query->get('amount', 10);
        $pageNumber = $request->query->get('page', 1);

        $data = $floorRepository->findBuildingFloors($building, $filter, $amountPerPage, $pageNumber, $reply);

        $paginator = new Paginator($data, $amountPerPage, $pageNumber);
        if ($paginator->isFromGreaterThanTotal()) {
            $number = ($pageNumber === 1) ? 1 : ($pageNumber - 1);
            return new RedirectResponse($this->generateUrl($request->attributes->get('_route'), [...$request->query->all(), 'page' => $number]), Response::HTTP_SEE_OTHER);
        }

        $template = ($request->isXmlHttpRequest()) ? '_list.html.twig' : 'index.html.twig';

        return $this->render("floor/$template", [
            'filter' => $filter,
            'paginator' => $paginator,
            'building' => $building,
            'reply' => $reply
        ]);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/new/{building}/{reply}', name: 'app_floor_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CrudActionService $crudActionService, Building $building, bool $reply = false): Response
    {
        $floor = new Floor();
        return $crudActionService->formLiveComponentAction($request, $floor, 'floor', [
            'title' => 'Nueva Planta',
            'building' => $building,
            'reply' => $reply,
        ]);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/{id}', name: 'app_floor_show', methods: ['GET'])]
    public function show(Request $request, Floor $floor, CrudActionService $crudActionService): Response
    {
        return $crudActionService->showAction($request, $floor, 'floor', 'floor', 'Detalles de la planta');
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/{id}/edit/{building}', name: 'app_floor_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Floor $floor, CrudActionService $crudActionService, Building $building): Response
    {
        return $crudActionService->formLiveComponentAction($request, $floor, 'floor', [
            'title' => 'Editar Planta',
            'building' => $building
        ]);
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    #[Route('/{id}', name: 'app_floor_delete', methods: ['POST'])]
    public function delete(Request $request, Floor $floor, FloorRepository $floorRepository, CrudActionService $crudActionService): Response
    {
        $successMsg = 'Se ha eliminado la planta.';
        return $crudActionService->deleteAction($request, $floorRepository, $floor, $successMsg, 'app_building_index');
    }

}
