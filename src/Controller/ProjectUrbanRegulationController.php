<?php

namespace App\Controller;

use App\DTO\Paginator;
use App\Entity\Project;
use App\Entity\ProjectUrbanRegulation;
use App\Repository\ProjectUrbanRegulationRepository;
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

#[Route('/project/urban/regulation')]
final class ProjectUrbanRegulationController extends AbstractController
{
    #[Route('/{project}', name: 'app_project_urban_regulation_index', methods: ['GET'])]
    public function index(Request $request, RouterInterface $router, ProjectUrbanRegulationRepository $projectUrbanRegulationRepository, CrudActionService $crudActionService, Project $project): Response
    {
        $filter = $request->query->get('filter', '');
        $amountPerPage = (int) $request->query->get('amount', '10');
        $pageNumber = (int) $request->query->get('page', '1');

        $data = $projectUrbanRegulationRepository->findUrbanRegulationsInProject($project, $filter, $amountPerPage, $pageNumber);

        $paginator = new Paginator($data, $amountPerPage, $pageNumber);
        if ($paginator->isFromGreaterThanTotal()) {
            return $paginator->greatherThanTotal($request, $router, $pageNumber);
        }

        $template = ($request->isXmlHttpRequest()) ? '_list.html.twig' : 'index.html.twig';

        return $this->render("project_urban_regulation/$template", [
            'filter' => $filter,
            'paginator' => $paginator,
            'project' => $project->getId(),
        ]);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/new/{project}', name: 'app_project_urban_regulation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CrudActionService $crudActionService, Project $project): Response
    {
        $projectUrbanRegulation = new ProjectUrbanRegulation();

        return $crudActionService->formLiveComponentAction($request, $projectUrbanRegulation, 'project_urban_regulation', [
            'title' => 'Agregar regulación al proyecto',
            'project' => $project->getId(),
        ]);
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    #[Route('/{id}/{project}', name: 'app_project_urban_regulation_show', methods: ['GET'])]
    public function show(Request $request, ProjectUrbanRegulation $projectUrbanRegulation, CrudActionService $crudActionService, Project $project): Response
    {
        return $crudActionService->showAction($request, $projectUrbanRegulation, 'project_urban_regulation', 'project_urban_regulation', 'Detalles de la regulación en el proyecto');
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/{id}/edit/{project}', name: 'app_project_urban_regulation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ProjectUrbanRegulation $projectUrbanRegulation, CrudActionService $crudActionService, Project $project): Response
    {
        return $crudActionService->formLiveComponentAction($request, $projectUrbanRegulation, 'project_urban_regulation', [
            'title' => 'Editar regulación del proyecto',
        ]);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/{id}/{project}', name: 'app_project_urban_regulation_delete', methods: ['POST'])]
    public function delete(Request $request, ProjectUrbanRegulation $projectUrbanRegulation, ProjectUrbanRegulationRepository $projectUrbanRegulationRepository, CrudActionService $crudActionService, Project $project): Response
    {
        $successMsg = 'Se ha eliminado la regulación del proyecto.';
        $response = $crudActionService->deleteAction($request, $projectUrbanRegulationRepository, $projectUrbanRegulation, $successMsg, 'app_project_edit', ['id' => $project->getId()]);
        if ($response instanceof RedirectResponse) {
            $this->addFlash('success', $successMsg);

            return $response;
        }

        return $response;
    }
}
