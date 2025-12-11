<?php

namespace App\Controller;

use App\Entity\Role;
use App\Entity\SubsystemSubType;
use App\Repository\SubsystemSubTypeRepository;
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

#[Route('/subsystem/sub/type')]
#[IsGranted(Role::ROLE_ADMIN)]
final class SubsystemSubTypeController extends AbstractController
{
    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route(name: 'app_subsystem_sub_type_index', methods: ['GET'])]
    public function index(Request $request, SubsystemSubTypeRepository $subsystemSubTypeRepository, CrudActionService $crudActionService): Response
    {
        return $crudActionService->indexAction($request, $subsystemSubTypeRepository, 'findSubsystemSubtypes', 'subsystem_sub_type');
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/new', name: 'app_subsystem_sub_type_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CrudActionService $crudActionService): Response
    {
        $subsystemSubType = new SubsystemSubType();

        return $crudActionService->formLiveComponentAction($request, $subsystemSubType, 'subsystem_sub_type', [
            'title' => 'Nuevo sub tipo',
        ]);
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    #[Route('/{id}', name: 'app_subsystem_sub_type_show', methods: ['GET'])]
    public function show(Request $request, SubsystemSubType $subsystemSubType, CrudActionService $crudActionService): Response
    {
        return $crudActionService->showAction($request, $subsystemSubType, 'subsystem_sub_type', 'subsystem_sub_type', 'Detalles del sub tipo');
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/{id}/edit', name: 'app_subsystem_sub_type_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, SubsystemSubType $subsystemSubType, CrudActionService $crudActionService): Response
    {
        return $crudActionService->formLiveComponentAction($request, $subsystemSubType, 'subsystem_sub_type', [
            'title' => 'Editar sub tipo',
        ]);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/{id}', name: 'app_subsystem_sub_type_delete', methods: ['POST'])]
    public function delete(Request $request, SubsystemSubType $subsystemSubType, SubsystemSubTypeRepository $subsystemSubTypeRepository, CrudActionService $crudActionService): Response
    {
        $successMsg = 'Se ha eliminado el sub tipo.';
        $response = $crudActionService->deleteAction($request, $subsystemSubTypeRepository, $subsystemSubType, $successMsg, 'app_subsystem_sub_type_index');
        if ($response instanceof RedirectResponse) {
            $this->addFlash('success', $successMsg);

            return $response;
        }

        return $response;
    }
}
