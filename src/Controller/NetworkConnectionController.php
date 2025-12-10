<?php

namespace App\Controller;

use App\Entity\NetworkConnection;
use App\Entity\Role;
use App\Repository\NetworkConnectionRepository;
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

#[Route('/network/connection')]
#[IsGranted(Role::ROLE_ADMIN)]
final class NetworkConnectionController extends AbstractController
{
    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route(name: 'app_network_connection_index', methods: ['GET'])]
    public function index(Request $request, NetworkConnectionRepository $networkConnectionRepository, CrudActionService $crudActionService): Response
    {
        return $crudActionService->indexAction($request, $networkConnectionRepository, 'findNetworkConnections', 'network_connection');
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/new', name: 'app_network_connection_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CrudActionService $crudActionService): Response
    {
        $networkConnection = new NetworkConnection();
        return $crudActionService->formLiveComponentAction($request, $networkConnection, 'network_connection', [
            'title' => 'Nueva conexión de red',
        ]);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/{id}', name: 'app_network_connection_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(Request $request, NetworkConnection $networkConnection, CrudActionService $crudActionService): Response
    {
        return $crudActionService->showAction($request, $networkConnection, 'network_connection', 'network_connection', 'Detalles de la conexión de red');
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/{id}/edit', name: 'app_network_connection_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function edit(Request $request, NetworkConnection $networkConnection, CrudActionService $crudActionService): Response
    {
        return $crudActionService->formLiveComponentAction($request, $networkConnection, 'network_connection', [
            'title' => 'Editar conexión de red',
        ]);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/{id}', name: 'app_network_connection_delete', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function delete(Request $request, NetworkConnection $networkConnection, NetworkConnectionRepository $networkConnectionRepository, CrudActionService $crudActionService): Response
    {
        $successMsg = 'Se ha eliminado la conexión de red.';
        $response = $crudActionService->deleteAction($request, $networkConnectionRepository, $networkConnection, $successMsg, 'app_network_connection_index');
        if($response instanceof RedirectResponse){
            $this->addFlash('success', $successMsg);
            return $response;
        }

        return $response;
    }

}
