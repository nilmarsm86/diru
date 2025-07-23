<?php

namespace App\Controller;

use App\Entity\Building;
use App\Entity\Role;
use App\Form\BuildingType;
use App\Repository\BuildingRepository;
use App\Service\CrudActionService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

#[IsGranted(Role::ROLE_DRAFTSMAN)]
#[Route('/building')]
final class BuildingController extends AbstractController
{
    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route(name: 'app_building_index', methods: ['GET'])]
    public function index(Request $request, BuildingRepository $buildingRepository, CrudActionService $crudActionService): Response
    {
        return $crudActionService->indexAction($request, $buildingRepository, 'findBuildings', 'building');
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    #[Route('/new', name: 'app_building_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CrudActionService $crudActionService): Response
    {
        $building = new Building();
        return $crudActionService->formLiveComponentAction($request, $building, 'building', [
            'title' => 'Nueva Obra',
        ]);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/{id}', name: 'app_building_show', methods: ['GET'])]
    public function show(Request $request, Building $building, CrudActionService $crudActionService): Response
    {
        return $crudActionService->showAction($request, $building, 'building', 'building', 'Detalles de la Obra');
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/{id}/edit', name: 'app_building_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Building $building, CrudActionService $crudActionService): Response
    {
        return $crudActionService->formLiveComponentAction($request, $building, 'building', [
            'title' => 'Editar Obra',
        ]);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[IsGranted(Role::ROLE_ADMIN)]
    #[Route('/{id}', name: 'app_building_delete', methods: ['POST'])]
    public function delete(Request $request, Building $building, BuildingRepository $buildingRepository, CrudActionService $crudActionService): Response
    {
        $successMsg = 'Se ha eliminado la obra.';
        return $crudActionService->deleteAction($request, $buildingRepository, $building, $successMsg, 'app_building_index');
    }

    #[Route('/reply/{id}', name: 'app_building_reply', methods: ['GET'])]
    public function reply(Request $request, Building $building, EntityManagerInterface $entityManager): Response
    {
        try {
            $building->reply($entityManager);
            $this->addFlash('success', 'Se ha replicado la obra');
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
        }

        return $this->redirectToRoute('app_floor_index', ['building' => $building->getId()]);
    }
}
