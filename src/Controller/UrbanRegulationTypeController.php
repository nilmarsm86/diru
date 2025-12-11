<?php

namespace App\Controller;

use App\Entity\Role;
use App\Entity\UrbanRegulationType;
use App\Repository\UrbanRegulationTypeRepository;
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

#[IsGranted(Role::ROLE_DRAFTSMAN)]
#[Route('/urban/regulation-type')]
final class UrbanRegulationTypeController extends AbstractController
{
    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route(name: 'app_urban_regulation_type_index', methods: ['GET'])]
    public function index(Request $request, UrbanRegulationTypeRepository $urbanRegulationTypeRepository, CrudActionService $crudActionService): Response
    {
        return $crudActionService->indexAction($request, $urbanRegulationTypeRepository, 'findUrbanRegulationTypes', 'urban_regulation_type');
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/new', name: 'app_urban_regulation_type_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CrudActionService $crudActionService): Response
    {
        $urbanRegulationType = new UrbanRegulationType();

        return $crudActionService->formLiveComponentAction($request, $urbanRegulationType, 'urban_regulation_type', [
            'title' => 'Nuevo tipo de regulación urbana',
        ]);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/{id}', name: 'app_urban_regulation_type_show', methods: ['GET'])]
    public function show(Request $request, UrbanRegulationType $urbanRegulationType, CrudActionService $crudActionService): Response
    {
        return $crudActionService->showAction($request, $urbanRegulationType, 'urban_regulation_type', 'urban_regulation_type', 'Detalles del tipo de regulación urbana');
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    #[Route('/{id}/edit', name: 'app_urban_regulation_type_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, UrbanRegulationType $urbanRegulationType, CrudActionService $crudActionService): Response
    {
        return $crudActionService->formLiveComponentAction($request, $urbanRegulationType, 'urban_regulation_type', [
            'title' => 'Editar tipo de regulación urbana',
        ]);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/{id}', name: 'app_urban_regulation_type_delete', methods: ['POST'])]
    public function delete(Request $request, UrbanRegulationType $urbanRegulationType, UrbanRegulationTypeRepository $urbanRegulationTypeRepository, CrudActionService $crudActionService): Response
    {
        $successMsg = 'Se ha eliminado el tipo de regulación urbana.';
        $response = $crudActionService->deleteAction($request, $urbanRegulationTypeRepository, $urbanRegulationType, $successMsg, 'app_urban_regulation_type_index');
        if ($response instanceof RedirectResponse) {
            $this->addFlash('success', $successMsg);

            return $response;
        }

        return $response;
    }
}
