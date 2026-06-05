<?php

namespace App\Controller;

use App\Entity\IteProjectType;
use App\Entity\Role;
use App\Repository\IteProjectTypeRepository;
use App\Service\CrudActionService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

#[IsGranted(Role::ROLE_ADMIN)]
#[Route('/ite/project/type')]
final class IteProjectTypeController extends AbstractController
{
    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route(name: 'app_ite_project_type_index', methods: ['GET'])]
    public function index(Request $request, IteProjectTypeRepository $iteProjectTypeRepository, CrudActionService $crudActionService): Response
    {
        return $crudActionService->indexAction($request, $iteProjectTypeRepository, 'findIteProjectTypes', 'ite_project_type');
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/new', name: 'app_ite_project_type_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CrudActionService $crudActionService): Response
    {
        $iteProjectType = new IteProjectType();

        return $crudActionService->formLiveComponentAction($request, $iteProjectType, 'ite_project_type', [
            'title' => 'Nuevo tipo de proyecto de ITE',
        ]);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/{id}', name: 'app_ite_project_type_show', methods: ['GET'])]
    public function show(Request $request, IteProjectType $iteProjectType, CrudActionService $crudActionService): Response
    {
        return $crudActionService->showAction($request, $iteProjectType, 'ite_project_type', 'ite_project_type', 'Detalles del tipo de proyecto de ITE');
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    #[Route('/{id}/edit', name: 'app_ite_project_type_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, IteProjectType $iteProjectType, CrudActionService $crudActionService): Response
    {
        return $crudActionService->formLiveComponentAction($request, $iteProjectType, 'ite_project_type', [
            'title' => 'Editar tipo de proyecto de ITE',
        ]);
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    #[Route('/{id}', name: 'app_ite_project_type_delete', methods: ['POST'])]
    public function delete(Request $request, IteProjectType $iteProjectType, IteProjectTypeRepository $iteProjectTypeRepository, CrudActionService $crudActionService): Response
    {
        $successMsg = 'Se ha eliminado el tipo de proyecto de ITE.';
        $response = $crudActionService->deleteAction($request, $iteProjectTypeRepository, $iteProjectType, $successMsg, 'app_ite_project_type_index');
        if ($response instanceof RedirectResponse) {
            $this->addFlash('success', $successMsg);

            return $response;
        }

        return $response;
    }
}
