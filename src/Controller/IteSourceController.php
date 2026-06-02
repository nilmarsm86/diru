<?php

namespace App\Controller;

use App\Entity\IteSource;
use App\Entity\Role;
use App\Repository\IteSourceRepository;
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
#[Route('/ite/source')]
final class IteSourceController extends AbstractController
{
    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route(name: 'app_ite_source_index', methods: ['GET'])]
    public function index(Request $request, IteSourceRepository $iteSourceRepository, CrudActionService $crudActionService): Response
    {
        return $crudActionService->indexAction($request, $iteSourceRepository, 'findIteSources', 'ite_source');
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/new', name: 'app_ite_source_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CrudActionService $crudActionService): Response
    {
        $iteSource = new IteSource();

        return $crudActionService->formLiveComponentAction($request, $iteSource, 'ite_source', [
            'title' => 'Nueva fuente de información',
        ]);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/{id}', name: 'app_ite_source_show', methods: ['GET'])]
    public function show(Request $request, IteSource $iteSource, CrudActionService $crudActionService): Response
    {
        return $crudActionService->showAction($request, $iteSource, 'ite_source', 'ite_source', 'Detalles de la fuente de información');
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    #[Route('/{id}/edit', name: 'app_ite_source_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, IteSource $iteSource, CrudActionService $crudActionService): Response
    {
        return $crudActionService->formLiveComponentAction($request, $iteSource, 'ite_source', [
            'title' => 'Editar fuente de información',
        ]);
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    #[Route('/{id}', name: 'app_ite_source_delete', methods: ['POST'])]
    public function delete(Request $request, IteSource $iteSource, IteSourceRepository $iteSourceRepository, CrudActionService $crudActionService): Response
    {
        $successMsg = 'Se ha eliminado la fuente de información.';
        $response = $crudActionService->deleteAction($request, $iteSourceRepository, $iteSource, $successMsg, 'app_ite_source_index');
        if ($response instanceof RedirectResponse) {
            $this->addFlash('success', $successMsg);

            return $response;
        }

        return $response;
    }
}
