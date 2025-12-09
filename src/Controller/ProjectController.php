<?php

namespace App\Controller;

use App\DTO\Paginator;
use App\Entity\Enums\ProjectState;
use App\Entity\Enums\ProjectType;
use App\Entity\Project;
use App\Entity\Role;
use App\Repository\ProjectRepository;
use App\Service\CrudActionService;
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
#[Route('/project')]
final class ProjectController extends AbstractController
{
    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route(name: 'app_project_index', methods: ['GET'])]
    public function index(Request $request, RouterInterface $router, ProjectRepository $projectRepository, CrudActionService $crudActionService): Response
    {
        $filter = $request->query->get('filter', '');
        $amountPerPage = (int)$request->query->get('amount', '10');
        $pageNumber = (int)$request->query->get('page', '1');

        $type = $request->query->get('type', '');
        $state = $request->query->get('state', '');

        $data = $projectRepository->findProjects($filter, $amountPerPage, $pageNumber, $type, $state);

        $paginator = new Paginator($data, $amountPerPage, $pageNumber);
        if ($paginator->isFromGreaterThanTotal()) {
            return $paginator->greatherThanTotal($request, $router, $pageNumber);
        }

        $template = ($request->isXmlHttpRequest()) ? '_list.html.twig' : 'index.html.twig';

        return $this->render("project/$template", [
            'filter' => $filter,
            'paginator' => $paginator,
            'types' => ProjectType::cases(),
            'states' => ProjectState::cases(),
        ]);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/new', name: 'app_project_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CrudActionService $crudActionService): Response
    {
        $project = new Project();
        return $crudActionService->formLiveComponentAction($request, $project, 'project', [
            'title' => 'Nuevo proyecto',
        ]);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/{id}', name: 'app_project_show', methods: ['GET'])]
    public function show(Request $request,Project $project, CrudActionService $crudActionService): Response
    {
        return $crudActionService->showAction($request, $project, 'project', 'project', 'Detalles del proyecto');
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/{id}/edit', name: 'app_project_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Project $project, CrudActionService $crudActionService): Response
    {
        return $crudActionService->formLiveComponentAction($request, $project, 'project', [
            'title' => 'Editar proyecto',
        ]);
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    #[Route('/{id}', name: 'app_project_delete', methods: ['POST'])]
    public function delete(Request $request, Project $project, ProjectRepository $projectRepository, CrudActionService $crudActionService): Response
    {
        $successMsg = 'Se ha eliminado el proyecto.';
        return $crudActionService->deleteAction($request, $projectRepository, $project, $successMsg, 'app_project_index');
    }
}
