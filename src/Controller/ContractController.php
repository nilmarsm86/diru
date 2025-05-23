<?php

namespace App\Controller;

use App\Entity\Contract;
use App\Entity\Role;
use App\Repository\ContractRepository;
use App\Service\CrudActionService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

#[Route('/contract')]
final class ContractController extends AbstractController
{
    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    #[IsGranted(Role::ROLE_DRAFTSMAN)]
    #[Route(name: 'app_contract_index', methods: ['GET'])]
    public function index(Request $request, ContractRepository $contractRepository, CrudActionService $crudActionService): Response
    {
        return $crudActionService->indexAction($request, $contractRepository, 'findContracts', 'contract');
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[IsGranted(Role::ROLE_DRAFTSMAN)]
    #[Route('/new', name: 'app_contract_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CrudActionService $crudActionService): Response
    {
        $contract = new Contract();
        return $crudActionService->formLiveComponentAction($request, $contract, 'contract', [
            'title' => 'Nuevo contrato',
            'ajax' => $request->isXmlHttpRequest()
        ]);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[IsGranted(Role::ROLE_DRAFTSMAN)]
    #[Route('/{id}', name: 'app_contract_show', methods: ['GET'])]
    public function show(Request $request, Contract $contract, CrudActionService $crudActionService): Response
    {
        return $crudActionService->showAction($request, $contract, 'contract', 'contract', 'Detalles del contrato');
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    #[IsGranted(Role::ROLE_DRAFTSMAN)]
    #[Route('/{id}/edit', name: 'app_contract_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Contract $contract, CrudActionService $crudActionService): Response
    {
        return $crudActionService->formLiveComponentAction($request, $contract, 'contract', [
            'title' => 'Editar contrato',
            'ajax' => $request->isXmlHttpRequest()
        ]);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[IsGranted(Role::ROLE_ADMIN)]
    #[Route('/{id}', name: 'app_contract_delete', methods: ['POST'])]
    public function delete(Request $request, Contract $contract, ContractRepository $contractRepository, CrudActionService $crudActionService): Response
    {
        $successMsg = 'Se ha eliminado el contrato.';
        return $crudActionService->deleteAction($request, $contractRepository, $contract, $successMsg, 'app_contract_index');
    }
}
