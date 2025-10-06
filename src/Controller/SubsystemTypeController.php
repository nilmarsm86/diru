<?php

namespace App\Controller;

use App\Entity\Role;
use App\Entity\SubsystemType;
use App\Form\SubsystemTypeType;
use App\Repository\SubsystemTypeRepository;
use App\Service\CrudActionService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

#[Route('/subsystem/type')]
#[IsGranted(Role::ROLE_ADMIN)]
final class SubsystemTypeController extends AbstractController
{
    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route(name: 'app_subsystem_type_index', methods: ['GET'])]
    public function index(Request $request, SubsystemTypeRepository $subsystemTypeRepository, CrudActionService $crudActionService): Response
    {
        return $crudActionService->indexAction($request, $subsystemTypeRepository, 'findSubsystemsType', 'subsystem_type');
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/new', name: 'app_subsystem_type_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CrudActionService $crudActionService): Response
    {
        $subsystemType = new SubsystemType();
        return $crudActionService->formLiveComponentAction($request, $subsystemType, 'subsystem_type', [
            'title' => 'Nuevo tipo',
        ]);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/{id}', name: 'app_subsystem_type_show', methods: ['GET'])]
    public function show(Request $request, SubsystemType $subsystemType, CrudActionService $crudActionService): Response
    {
        return $crudActionService->showAction($request, $subsystemType, 'subsystem_type', 'subsystem_type', 'Detalles del tipo');
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    #[Route('/{id}/edit', name: 'app_subsystem_type_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, SubsystemType $subsystemType, CrudActionService $crudActionService): Response
    {
        return $crudActionService->formLiveComponentAction($request, $subsystemType, 'subsystem_type', [
            'title' => 'Editar tipo',
        ]);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/{id}', name: 'app_subsystem_type_delete', methods: ['POST'])]
    public function delete(Request $request, SubsystemType $subsystemType, SubsystemTypeRepository $subsystemTypeRepository, CrudActionService $crudActionService): Response
    {
        $successMsg = 'Se ha eliminado el tipo.';
        return $crudActionService->deleteAction($request, $subsystemTypeRepository, $subsystemType, $successMsg, 'app_subsystem_type_index');
    }
}
