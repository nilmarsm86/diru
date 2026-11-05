<?php

namespace App\Controller;

use App\DTO\Paginator;
use App\Entity\IndividualClient;
use App\Entity\Role;
use App\Repository\IndividualClientRepository;
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
#[Route('/individual/client')]
final class IndividualClientController extends AbstractController
{
    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route(name: 'app_individual_client_index', methods: ['GET'])]
    public function index(Request $request, RouterInterface $router, IndividualClientRepository $individualClientRepository): Response
    {
        $filter = $request->query->get('filter', '');
        $amountPerPage = (int) $request->query->get('amount', '10');
        $pageNumber = (int) $request->query->get('page', '1');

        $representative = $request->query->get('representative', null) ?? null;

        $data = $individualClientRepository->findIndividuals($filter, $amountPerPage, $pageNumber, $representative);

        $paginator = new Paginator($data, $amountPerPage, $pageNumber);
        if ($paginator->isFromGreaterThanTotal()) {
            return $paginator->greatherThanTotal($request, $router, $pageNumber);
        }

        $template = ($request->isXmlHttpRequest()) ? '_list.html.twig' : 'index.html.twig';

        return $this->render("individual_client/$template", [
            'filter' => $filter,
            'paginator' => $paginator,
            'representatives' => [IndividualClientRepository::WITH_REPRESENTATIVE, IndividualClientRepository::WITHOUT_REPRESENTATIVE],
        ]);
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    #[Route('/new', name: 'app_individual_client_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CrudActionService $crudActionService): Response
    {
        $individualClient = new IndividualClient();

        return $crudActionService->formLiveComponentAction($request, $individualClient, 'individual_client', [
            'title' => 'Nueva persona natural',
        ]);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/{id}', name: 'app_individual_client_show', methods: ['GET'])]
    public function show(Request $request, IndividualClient $individualClient, CrudActionService $crudActionService): Response
    {
        return $crudActionService->showAction($request, $individualClient, 'individual_client', 'individual_client', 'Detalles del cliente');
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    #[Route('/{id}/edit', name: 'app_individual_client_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, IndividualClient $individualClient, CrudActionService $crudActionService): Response
    {
        return $crudActionService->formLiveComponentAction($request, $individualClient, 'individual_client', [
            'title' => 'Editar persona individual',
        ]);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[IsGranted(Role::ROLE_ADMIN)]
    #[Route('/{id}', name: 'app_individual_client_delete', methods: ['POST'])]
    public function delete(Request $request, IndividualClient $individualClient, IndividualClientRepository $individualClientRepository, CrudActionService $crudActionService): Response
    {
        $successMsg = 'Se ha eliminado el cliente.';
        $response = $crudActionService->deleteAction($request, $individualClientRepository, $individualClient, $successMsg, 'app_enterprise_client_index');
        if ($response instanceof RedirectResponse) {
            $this->addFlash('success', $successMsg);

            return $response;
        }

        return $response;
    }
}
