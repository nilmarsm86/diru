<?php

namespace App\Controller;

use App\Entity\Building;
use App\Entity\JustValueEstimate;
use App\Entity\Role;
use App\Repository\JustValueEstimateRepository;
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
#[Route('/just_value/estimate')]
final class JustValueEstimateController extends AbstractController
{
    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/{building}', name: 'app_just_value_estimate_index', methods: ['GET'])]
    public function index(Request $request, JustValueEstimateRepository $justValueEstimateRepository, CrudActionService $crudActionService, Building $building): Response
    {
        return $crudActionService->indexAction($request, $justValueEstimateRepository, 'findJustValueEstimates', 'just_value_estimate', ['building' => $building->getId()]);
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    #[Route('/new/{building}', name: 'app_just_value_estimate_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CrudActionService $crudActionService, ?Building $building = null): Response
    {
        $justValueEstimate = new JustValueEstimate();

        return $crudActionService->formLiveComponentAction($request, $justValueEstimate, 'just_value_estimate', [
            'title' => 'Nuevo valor estimado ajustado',
            'building' => $building,
        ]);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/{id}/{building}', name: 'app_just_value_estimate_show', methods: ['GET'])]
    public function show(Request $request, JustValueEstimate $justValueEstimate, CrudActionService $crudActionService, ?Building $building = null): Response
    {
        return $crudActionService->showAction($request, $justValueEstimate, 'just_value_estimate', 'just_value_estimate', 'Detalles del valor estimado ajustado', [
            'building' => $building,
        ]);
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    #[Route('/{id}/edit/{building}', name: 'app_just_value_estimate_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, JustValueEstimate $justValueEstimate, CrudActionService $crudActionService, ?Building $building = null): Response
    {
        return $crudActionService->formLiveComponentAction($request, $justValueEstimate, 'just_value_estimate', [
            'title' => 'Editar valor estimado ajustado',
            'building' => $building,
        ]);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/{id}/{building}', name: 'app_just_value_estimate_delete', methods: ['POST'])]
    public function delete(Request $request, JustValueEstimate $justValueEstimate, JustValueEstimateRepository $justValueEstimateRepository, CrudActionService $crudActionService, Building $building): Response
    {
        $successMsg = 'Se ha eliminado el valor estimado ajustado.';
        $response = $crudActionService->deleteAction($request, $justValueEstimateRepository, $justValueEstimate, $successMsg, 'app_building_edit', [
            'id' => $building->getId(),
            'project' => $building->getProject()?->getId(),
        ]);
        if ($response instanceof RedirectResponse) {
            $this->addFlash('success', $successMsg);

            return $response;
        }

        return $response;
    }
}
