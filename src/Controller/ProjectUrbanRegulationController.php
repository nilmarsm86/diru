<?php

namespace App\Controller;

use App\Entity\ProjectUrbanRegulation;
use App\Repository\ProjectUrbanRegulationRepository;
use App\Service\CrudActionService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/project/urban/regulation')]
final class ProjectUrbanRegulationController extends AbstractController
{
    #[Route(name: 'app_project_urban_regulation_index', methods: ['GET'])]
    public function index(Request $request, ProjectUrbanRegulationRepository $projectUrbanRegulationRepository, CrudActionService $crudActionService): Response
    {
        return $crudActionService->indexAction($request, $projectUrbanRegulationRepository, 'findUrbanRegulationsInProject', 'project_urban_regulation');
    }

    #[Route('/new', name: 'app_project_urban_regulation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CrudActionService $crudActionService): Response
    {
        $projectUrbanRegulation = new ProjectUrbanRegulation();

        return $crudActionService->formLiveComponentAction($request, $projectUrbanRegulation, 'project_urban_regulation', [
            'title' => 'Agregar regulación al proyecto',
        ]);
    }

    #[Route('/{id}', name: 'app_project_urban_regulation_show', methods: ['GET'])]
    public function show(Request $request, ProjectUrbanRegulation $projectUrbanRegulation, CrudActionService $crudActionService): Response
    {
        return $crudActionService->showAction($request, $projectUrbanRegulation, 'project_urban_regulation', 'project_urban_regulation', 'Detalles de la regulación en el proyecto');
    }

    #[Route('/{id}/edit', name: 'app_project_urban_regulation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ProjectUrbanRegulation $projectUrbanRegulation, CrudActionService $crudActionService): Response
    {
        return $crudActionService->formLiveComponentAction($request, $projectUrbanRegulation, 'project_urban_regulation', [
            'title' => 'Editar regulación del proyecto',
        ]);
    }

    #[Route('/{id}', name: 'app_project_urban_regulation_delete', methods: ['POST'])]
    public function delete(Request $request, ProjectUrbanRegulation $projectUrbanRegulation, ProjectUrbanRegulationRepository $projectUrbanRegulationRepository, CrudActionService $crudActionService): Response
    {
        $successMsg = 'Se ha eliminado la regulación del proyecto.';
        $response = $crudActionService->deleteAction($request, $projectUrbanRegulationRepository, $projectUrbanRegulation, $successMsg, 'app_project_urban_regulation_index');
        if ($response instanceof RedirectResponse) {
            $this->addFlash('success', $successMsg);

            return $response;
        }

        return $response;
    }
}
