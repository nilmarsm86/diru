<?php

namespace App\Controller;

use App\Entity\Building;
use App\Entity\Land;
use App\Repository\LandRepository;
use App\Service\CrudActionService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

#[Route('/land')]
final class LandController extends AbstractController
{
    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    #[Route(name: 'app_land_index', methods: ['GET'])]
    public function index(Request $request, LandRepository $landRepository, CrudActionService $crudActionService): Response
    {
        return $crudActionService->indexAction($request, $landRepository, 'findLands', 'land');
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/new/{building}', name: 'app_land_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CrudActionService $crudActionService, Building $building): Response
    {
        $land = new Land();

        return $crudActionService->formLiveComponentAction($request, $land, 'land', [
            'title' => 'Nuevos datos generales del terreno',
            'building' => $building,
        ]);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/{id}', name: 'app_land_show', methods: ['GET'])]
    public function show(Request $request, Land $land, CrudActionService $crudActionService): Response
    {
        return $crudActionService->showAction($request, $land, 'land', 'land', 'Detalles del terreno');
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    #[Route('/{id}/edit/{building}', name: 'app_land_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Land $land, CrudActionService $crudActionService, Building $building): Response
    {
        return $crudActionService->formLiveComponentAction($request, $land, 'land', [
            'title' => 'Editar datos generales del terreno',
            'building' => $building,
        ]);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/{id}', name: 'app_land_delete', methods: ['POST'])]
    public function delete(Request $request, Land $land, LandRepository $landRepository, CrudActionService $crudActionService): Response
    {
        $successMsg = 'Se ha eliminado los datos del terreno.';
        $response = $crudActionService->deleteAction($request, $landRepository, $land, $successMsg, 'app_land_index');
        if ($response instanceof RedirectResponse) {
            $this->addFlash('success', $successMsg);

            return $response;
        }

        return $response;
    }

    #[Route('/unassigned-free/{id}/{building}/{reply}', name: 'app_land_unassigned-free', methods: ['GET'])]
    public function unassignedOrFree(Land $land, LandRepository $landRepository, Building $building, bool $reply = false): Response
    {
        $land->setIsBlocked(!(bool) $land->isBlocked());
        $landRepository->save($land, true);

        $this->addFlash('success', 'Se ha transformado el área.');

        return $this->redirectToRoute('app_floor_index', ['building' => $building->getId(), 'reply' => $reply], Response::HTTP_SEE_OTHER);
    }
}
