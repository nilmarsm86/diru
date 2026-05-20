<?php

namespace App\Controller;

use App\DTO\Paginator;
use App\Entity\Ite;
use App\Repository\IteRepository;
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

#[Route('/ite_manage')]
final class IteController extends AbstractController
{
    #[Route(name: 'app_ite_index', methods: ['GET'])]
    public function index(Request $request, RouterInterface $router, IteRepository $iteRepository): Response
    {
        $filter = $request->query->get('filter', '');
        $amountPerPage = (int) $request->query->get('amount', '10');
        $pageNumber = (int) $request->query->get('page', '1');

        //        $type = $request->query->get('entity', '');

        $data = $iteRepository->findItes($filter, $amountPerPage, $pageNumber);

        $paginator = new Paginator($data, $amountPerPage, $pageNumber);
        if ($paginator->isFromGreaterThanTotal()) {
            return $paginator->greatherThanTotal($request, $router, $pageNumber);
        }

        $template = ($request->isXmlHttpRequest()) ? '_list.html.twig' : 'index.html.twig';

        return $this->render("ite/$template", [
            'filter' => $filter,
            'paginator' => $paginator,
            //            'types' => CorporateEntityType::cases(),
        ]);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/new', name: 'app_ite_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CrudActionService $crudActionService): Response
    {
        $ite = new Ite();

        return $crudActionService->formLiveComponentAction($request, $ite, 'ite', [
            'title' => 'Nuevo Indicador Técnico Económico',
        ]);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/{id}', name: 'app_ite_show', methods: ['GET'])]
    public function show(Request $request, Ite $ite, CrudActionService $crudActionService): Response
    {
        return $crudActionService->showAction($request, $ite, 'ite', 'ite', 'Detalles del indicador');
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/{id}/edit', name: 'app_ite_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Ite $ite, CrudActionService $crudActionService): Response
    {
        return $crudActionService->formLiveComponentAction($request, $ite, 'ite', [
            'title' => 'Editar Indicador Técnico Económico',
        ]);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/{id}', name: 'app_ite_delete', methods: ['POST'])]
    public function delete(Request $request, Ite $ite, IteRepository $iteRepository, CrudActionService $crudActionService): Response
    {
        $successMsg = 'Se ha eliminado el inidicador.';
        $response = $crudActionService->deleteAction($request, $iteRepository, $ite, $successMsg, 'app_ite_index');
        if ($response instanceof RedirectResponse) {
            $this->addFlash('success', $successMsg);

            return $response;
        }

        return $response;
    }
}
