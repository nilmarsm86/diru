<?php

namespace App\Controller;

use App\Entity\EnterpriseClient;
use App\Entity\Role;
use App\Repository\EnterpriseClientRepository;
use App\Service\CrudActionService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

#[IsGranted(Role::ROLE_DRAFTSMAN)]
#[Route('/enterprise/client')]
final class EnterpriseClientController extends AbstractController
{
    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route(name: 'app_enterprise_client_index', methods: ['GET'])]
    public function index(Request $request, EnterpriseClientRepository $enterpriseClientRepository, CrudActionService $crudActionService): Response
    {
        return $crudActionService->indexAction($request, $enterpriseClientRepository, 'findEnterprises', 'enterprise_client');
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    #[Route('/new', name: 'app_enterprise_client_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CrudActionService $crudActionService): Response
    {
        $enterpriseClient = new EnterpriseClient();
        return $crudActionService->formLiveComponentAction($request, $enterpriseClient, 'enterprise_client', [
            'title' => 'Nuevo cliente empresarial',
            'ajax' => $request->isXmlHttpRequest()
        ]);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/{id}', name: 'app_enterprise_client_show', methods: ['GET'])]
    public function show(Request $request, EnterpriseClient $enterpriseClient, CrudActionService $crudActionService): Response
    {
        return $crudActionService->showAction($request, $enterpriseClient, 'enterprise_client', 'enterprise_client', 'Detalles del cliente');
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    #[Route('/{id}/edit', name: 'app_enterprise_client_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, EnterpriseClient $enterpriseClient, CrudActionService $crudActionService): Response
    {
        return $crudActionService->formLiveComponentAction($request, $enterpriseClient, 'enterprise_client', [
            'title' => 'Modificar cliente empresarial',
            'ajax' => $request->isXmlHttpRequest()
        ]);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[IsGranted(Role::ROLE_ADMIN)]
    #[Route('/{id}', name: 'app_enterprise_client_delete', methods: ['POST'])]
    public function delete(Request $request, EnterpriseClient $enterpriseClient, EnterpriseClientRepository $enterpriseClientRepository, CrudActionService $crudActionService): Response
    {
        $successMsg = 'Se ha eliminado el cliente empresarial.';
        return $crudActionService->deleteAction($request, $enterpriseClientRepository, $enterpriseClient, $successMsg, 'app_enterprise_client_index');
    }
}
