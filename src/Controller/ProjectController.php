<?php

namespace App\Controller;

use App\Controller\Traits\PdfResponseTrait;
use App\DTO\Paginator;
use App\Entity\EnterpriseClient;
use App\Entity\Enums\ProjectState;
use App\Entity\Enums\ProjectType;
use App\Entity\IndividualClient;
use App\Entity\Project;
use App\Entity\Role;
use App\Repository\ProjectRepository;
use App\Service\CrudActionService;
use App\Service\Pdf\PdfAssetManager;
use App\Service\Pdf\PdfGenerator;
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
    use PdfResponseTrait;

    #[Route(name: 'app_project_index', methods: ['GET'])]
    public function index(Request $request, RouterInterface $router, ProjectRepository $projectRepository): Response
    {
        $filter = $request->query->get('filter', '');
        $amountPerPage = (int) $request->query->get('amount', '10');
        $pageNumber = (int) $request->query->get('page', '1');

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
    #[Route('/{id}', name: 'app_project_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(Request $request, Project $project, CrudActionService $crudActionService): Response
    {
        return $crudActionService->showAction($request, $project, 'project', 'project', 'Detalles del proyecto');
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/{id}/edit', name: 'app_project_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
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
    #[Route('/{id}', name: 'app_project_delete', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function delete(Request $request, Project $project, ProjectRepository $projectRepository, CrudActionService $crudActionService): Response
    {
        $successMsg = 'Se ha eliminado el proyecto.';
        $response = $crudActionService->deleteAction($request, $projectRepository, $project, $successMsg, 'app_project_index');
        if ($response instanceof RedirectResponse) {
            $this->addFlash('success', $successMsg);

            return $response;
        }

        return $response;
    }

    #[Route('/print_list', name: 'app_project_print_list', methods: ['GET'])]
    public function printList(Request $request, ProjectRepository $projectRepository, PdfAssetManager $pdfAssetManager, PdfGenerator $pdfGenerator): Response
    {
        $filter = $request->query->get('filter', '');

        $data = $projectRepository->findProjects($filter, null, null);

        $paginator = new Paginator($data);

        return $this->renderPdf($filter, $paginator, $pdfAssetManager, $pdfGenerator, 'project/pdf/print_list.html.twig', 'Listado de proyectos', 'municipios');
    }

    #[Route('/{id}/print', name: 'app_project_print', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function print(Request $request, Project $project, PdfAssetManager $pdfAssetManager, PdfGenerator $pdfGenerator): Response
    {
        $client = $project->getClient();
        $clientType = ($client instanceof IndividualClient) ? 'Persona natural' : 'Cliente Empresarial-Negocio';
        $clientName = '';
        $corporateEntity = null;
        if ($client instanceof EnterpriseClient) {
            $clientName = $client->getCorporateEntity()?->getName();
            $corporateEntity = $client->getCorporateEntity();
        }

        if ($client instanceof IndividualClient) {
            $clientName = $client->getPerson()?->getFullName();
        }

        $representative = $client?->getRepresentative();

        $html = $this->renderView('project/pdf/print.html.twig', [
            'logo' => $pdfAssetManager->getLogoBase64(),
            'title' => 'Proyecto: '.$project->getName(),
            'project' => $project,
            'client_type' => $clientType,
            'client_name' => $clientName,
            'representative' => $representative,
            'corporate_entity' => $corporateEntity,
            'client' => $client,
        ]);

        $pdfContent = $pdfGenerator->generate($html);

        return $this->pdfResponse($pdfContent, 'proyecto');
    }
}
