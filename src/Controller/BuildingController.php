<?php

namespace App\Controller;

use App\DTO\Paginator;
use App\Entity\Building;
use App\Entity\Enums\BuildingState;
use App\Entity\Project;
use App\Entity\Role;
use App\Repository\BuildingRepository;
use App\Service\CrudActionService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\RouterInterface;
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
        return $crudActionService->indexAction($request, $buildingRepository, 'findBuildings', 'building', [
            'project' => null,
        ]);
    }

    #[Route('/project/{project}', name: 'app_building_project', methods: ['GET'])]
    public function project(Request $request, RouterInterface $router, BuildingRepository $buildingRepository, Project $project): Response
    {
        $filter = $request->query->get('filter', '');
        $amountPerPage = (int) $request->query->get('amount', '10');
        $pageNumber = (int) $request->query->get('page', '1');

        $state = $request->query->get('state', '');

        $data = $buildingRepository->findBuildingsByProject($project, $filter, $amountPerPage, $pageNumber, $state);

        $paginator = new Paginator($data, $amountPerPage, $pageNumber);
        if ($paginator->isFromGreaterThanTotal()) {
            return $paginator->greatherThanTotal($request, $router, $pageNumber);
        }

        $template = ($request->isXmlHttpRequest()) ? '_list.html.twig' : 'index.html.twig';

        return $this->render("building/$template", [
            'filter' => $filter,
            'paginator' => $paginator,
            'project' => $project,
            'states' => BuildingState::cases(),
        ]);
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    #[Route('/new/{project}', name: 'app_building_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CrudActionService $crudActionService, ?Project $project = null): Response
    {
        $building = new Building();

        return $crudActionService->formLiveComponentAction($request, $building, 'building', [
            'title' => 'Nueva Obra',
            'project' => $project,
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
    #[Route('/{id}/edit/{project}', name: 'app_building_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Building $building, CrudActionService $crudActionService, ?Project $project = null): Response
    {
        return $crudActionService->formLiveComponentAction($request, $building, 'building', [
            'title' => 'Editar Obra',
            'project' => $project,
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
        $response = $crudActionService->deleteAction($request, $buildingRepository, $building, $successMsg, 'app_building_index');
        if ($response instanceof RedirectResponse) {
            $this->addFlash('success', $successMsg);

            return $response;
        }

        return $response;
    }

    #[Route('/reply/{id}', name: 'app_building_reply', methods: ['GET'])]
    public function reply(Building $building, EntityManagerInterface $entityManager): Response
    {
        try {
            $building->reply($entityManager);
            $this->addFlash('success', 'Se ha replicado la obra');
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
        }

        return $this->redirectToRoute('app_floor_index', ['building' => $building->getId(), 'reply' => true]);
    }

    // TODO: agrupar en un solo metodo el cambio de estado y en dependencia del tipo de estado se gestionan sus revisiones
    #[Route('/review/{id}', name: 'app_building_review', methods: ['GET'])]
    public function review(Building $building, EntityManagerInterface $entityManager): Response
    {
        try {
            $building->review($entityManager);
            $this->addFlash('success', 'Se ha pasado a estado de revisión');
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
        }

        return $this->redirectToRoute('app_building_edit', ['id' => $building->getId()]);
    }

    // TODO: agrupar en un solo metodo el cambio de estado y en dependencia del tipo de estado se gestionan sus revisiones
    #[Route('/design/{id}', name: 'app_building_design', methods: ['GET'])]
    public function design(Building $building, EntityManagerInterface $entityManager): Response
    {
        try {
            $building->design($entityManager);
            $this->addFlash('success', 'Se ha pasado a estado de diseño');
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
        }

        return $this->redirectToRoute('app_building_edit', ['id' => $building->getId()]);
    }

    // TODO: agrupar en un solo metodo el cambio de estado y en dependencia del tipo de estado se gestionan sus revisiones
    #[Route('/revised/{id}', name: 'app_building_revised', methods: ['GET'])]
    public function revised(Building $building, EntityManagerInterface $entityManager): Response
    {
        try {
            $building->revised($entityManager);
            $this->addFlash('success', 'Se ha pasado a estado de revisado');
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
        }

        return $this->redirectToRoute('app_building_edit', ['id' => $building->getId()]);
    }

    #[Route('/{id}/report/local', name: 'app_building_report_local', methods: ['GET'])]
    public function reportLocal(Building $building): Response
    {
        return $this->render('building/report.html.twig', [
            'local_status' => $building->getAmountTechnicalStatus(),
            'meter_status' => $building->getAmountMeterTechnicalStatus(),
            'title' => 'Estado técnico de los locales de la obra',
            'building' => $building,
        ]);
    }
}
