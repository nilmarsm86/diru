<?php

namespace App\Controller;

use App\Entity\ConstructiveSystem;
use App\Entity\Role;
use App\Repository\ConstructiveSystemRepository;
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

#[IsGranted(Role::ROLE_DRAFTSMAN)]
#[Route('/constructive/system')]
final class ConstructiveSystemController extends AbstractController
{
    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    #[Route(name: 'app_constructive_system_index', methods: ['GET'])]
    public function index(Request $request, ConstructiveSystemRepository $constructiveSystemRepository, CrudActionService $crudActionService): Response
    {
        return $crudActionService->indexAction($request, $constructiveSystemRepository, 'findConstructiveSystems', 'constructive_system');
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/new', name: 'app_constructive_system_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CrudActionService $crudActionService): Response
    {
        $constructiveSystem = new ConstructiveSystem();

        return $crudActionService->formLiveComponentAction($request, $constructiveSystem, 'constructive_system', [
            'title' => 'Nuevo sistema constructivo',
        ]);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/{id}', name: 'app_constructive_system_show', methods: ['GET'])]
    public function show(Request $request, ConstructiveSystem $constructiveSystem, CrudActionService $crudActionService): Response
    {
        return $crudActionService->showAction($request, $constructiveSystem, 'constructive_system', 'constructive_system', 'Detalles del sistema constructivo.');
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    #[Route('/{id}/edit', name: 'app_constructive_system_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ConstructiveSystem $constructiveSystem, CrudActionService $crudActionService): Response
    {
        return $crudActionService->formLiveComponentAction($request, $constructiveSystem, 'constructive_system', [
            'title' => 'Editar sistema constructivo',
        ]);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/{id}', name: 'app_constructive_system_delete', methods: ['POST'])]
    public function delete(Request $request, ConstructiveSystem $constructiveSystem, ConstructiveSystemRepository $constructiveSystemRepository, CrudActionService $crudActionService): Response
    {
        $successMsg = 'Se ha eliminado el sistema constructivo.';
        $response = $crudActionService->deleteAction($request, $constructiveSystemRepository, $constructiveSystem, $successMsg, 'app_constructive_system_index');
        if ($response instanceof RedirectResponse) {
            $this->addFlash('success', $successMsg);

            return $response;
        }

        return $response;
    }
}
