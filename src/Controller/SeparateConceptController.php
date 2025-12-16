<?php

namespace App\Controller;

use App\Entity\SeparateConcept;
use App\Repository\SeparateConceptRepository;
use App\Service\CrudActionService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

#[Route('/separate/concept')]
final class SeparateConceptController extends AbstractController
{
    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route(name: 'app_separate_concept_index', methods: ['GET'])]
    public function index(Request $request, SeparateConceptRepository $separateConceptRepository, CrudActionService $crudActionService): Response
    {
        return $crudActionService->indexAction($request, $separateConceptRepository, 'findSeparateConcepts', 'separate_concept');
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/new', name: 'app_separate_concept_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CrudActionService $crudActionService): Response
    {
        $separateConcept = new SeparateConcept();

        return $crudActionService->formLiveComponentAction($request, $separateConcept, 'separate_concept', [
            'title' => 'Nuevo concepto',
        ]);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/{id}', name: 'app_separate_concept_show', methods: ['GET'])]
    public function show(Request $request, SeparateConcept $separateConcept, CrudActionService $crudActionService): Response
    {
        return $crudActionService->showAction($request, $separateConcept, 'separate_concept', 'separate_concept', 'Detalles del concepto');
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    #[Route('/{id}/edit', name: 'app_separate_concept_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, CrudActionService $crudActionService, SeparateConcept $separateConcept): Response
    {
        return $crudActionService->formLiveComponentAction($request, $separateConcept, 'separate_concept', [
            'title' => 'Editar concepto',
        ]);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/{id}', name: 'app_separate_concept_delete', methods: ['POST'])]
    public function delete(Request $request, SeparateConcept $separateConcept, SeparateConceptRepository $separateConceptRepository, CrudActionService $crudActionService): Response
    {
        $successMsg = 'Se ha eliminado el concepto.';
        $response = $crudActionService->deleteAction($request, $separateConceptRepository, $separateConcept, $successMsg, 'app_separate_concept_index');
        if ($response instanceof RedirectResponse) {
            $this->addFlash('success', $successMsg);

            return $response;
        }

        return $response;
    }
}
