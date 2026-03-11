<?php

namespace App\Controller;

use App\DTO\Paginator;
use App\Entity\ConstructiveAction;
use App\Entity\Enums\ConstructiveActionType;
use App\Repository\ConstructiveActionRepository;
use App\Service\CrudActionService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\RouterInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

#[Route('/constructive/action')]
final class ConstructiveActionController extends AbstractController
{
    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route(name: 'app_constructive_action_index', methods: ['GET'])]
    public function index(Request $request, RouterInterface $router, ConstructiveActionRepository $constructiveActionRepository): Response
    {
        $filter = $request->query->get('filter', '');
        $amountPerPage = (int) $request->query->get('amount', '10');
        $pageNumber = (int) $request->query->get('page', '1');

        $type = $request->query->get('type', '');

        $data = $constructiveActionRepository->findConstructiveActions($filter, $amountPerPage, $pageNumber, $type);

        $paginator = new Paginator($data, $amountPerPage, $pageNumber);
        if ($paginator->isFromGreaterThanTotal()) {
            return $paginator->greatherThanTotal($request, $router, $pageNumber);
        }

        $template = ($request->isXmlHttpRequest()) ? '_list.html.twig' : 'index.html.twig';

        return $this->render("constructive_action/$template", [
            'filter' => $filter,
            'paginator' => $paginator,
            'types' => ConstructiveActionType::cases(),
        ]);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/new', name: 'app_constructive_action_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CrudActionService $crudActionService): Response
    {
        $constructiveAction = new ConstructiveAction();

        return $crudActionService->formLiveComponentAction($request, $constructiveAction, 'constructive_action', [
            'title' => 'Nueva acción constructiva',
        ]);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/{id}', name: 'app_constructive_action_show', methods: ['GET'])]
    public function show(Request $request, ConstructiveAction $constructiveAction, CrudActionService $crudActionService): Response
    {
        return $crudActionService->showAction($request, $constructiveAction, 'constructive_action', 'constructive_action', 'Detalles de la accion constructiva');
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    #[Route('/{id}/edit', name: 'app_constructive_action_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ConstructiveAction $constructiveAction, CrudActionService $crudActionService): Response
    {
        return $crudActionService->formLiveComponentAction($request, $constructiveAction, 'constructive_action', [
            'title' => 'Editar acción constructiva',
        ]);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/{id}', name: 'app_constructive_action_delete', methods: ['POST'])]
    public function delete(Request $request, ConstructiveAction $constructiveAction, ConstructiveActionRepository $constructiveActionRepository, CrudActionService $crudActionService): Response
    {
        $successMsg = 'Se ha eliminado la acción constructiva.';
        $response = $crudActionService->deleteAction($request, $constructiveActionRepository, $constructiveAction, $successMsg, 'app_constructive_action_index');
        if ($response instanceof RedirectResponse) {
            $this->addFlash('success', $successMsg);

            return $response;
        }

        return $response;
    }
}
