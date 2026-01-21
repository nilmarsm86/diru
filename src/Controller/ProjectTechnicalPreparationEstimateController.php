<?php

namespace App\Controller;

use App\Entity\Building;
use App\Entity\ProjectTechnicalPreparationEstimate;
use App\Entity\Role;
use App\Repository\ProjectTechnicalPreparationEstimateRepository;
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
#[Route('/ptp/estimate')]
final class ProjectTechnicalPreparationEstimateController extends AbstractController
{
    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/{building}', name: 'app_ptp_estimate_index', methods: ['GET'])]
    public function index(Request $request, ProjectTechnicalPreparationEstimateRepository $projectTechnicalPreparationEstimateRepository, CrudActionService $crudActionService, Building $building): Response
    {
        return $crudActionService->indexAction($request, $projectTechnicalPreparationEstimateRepository, 'findProjectTechnicalPreparationEstimate', 'ptp_estimate', ['building' => $building->getId()]);
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    #[Route('/new/{building}', name: 'app_ptp_estimate_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CrudActionService $crudActionService, ?Building $building = null): Response
    {
        $ptpe = new ProjectTechnicalPreparationEstimate();

        return $crudActionService->formLiveComponentAction($request, $ptpe, 'ptp_estimate', [
            'title' => 'Nuevo estimado de proyecto y preparación técnica',
            'building' => $building,
        ]);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/{id}', name: 'app_ptp_estimate_show', methods: ['GET'])]
    public function show(Request $request, ProjectTechnicalPreparationEstimate $projectTechnicalPreparationEstimate, CrudActionService $crudActionService): Response
    {
        return $crudActionService->showAction($request, $projectTechnicalPreparationEstimate, 'ptp_estimate', 'ptp_estimate', 'Detalles del estimado de proyecto y preparación técnica');
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    #[Route('/{id}/edit', name: 'app_ptp_estimate_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ProjectTechnicalPreparationEstimate $projectTechnicalPreparationEstimate, CrudActionService $crudActionService): Response
    {
        return $crudActionService->formLiveComponentAction($request, $projectTechnicalPreparationEstimate, 'ptp_estimate', [
            'title' => 'Editar estimado de proyecto y preparación técnica',
        ]);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/{id}/{building}', name: 'app_ptp_estimate_delete', methods: ['POST'])]
    public function delete(Request $request, ProjectTechnicalPreparationEstimate $projectTechnicalPreparationEstimate, ProjectTechnicalPreparationEstimateRepository $projectTechnicalPreparationEstimateRepository, CrudActionService $crudActionService, Building $building): Response
    {
        $successMsg = 'Se ha eliminado el estimado de proyecto y preparación técnica.';
        $response = $crudActionService->deleteAction($request, $projectTechnicalPreparationEstimateRepository, $projectTechnicalPreparationEstimate, $successMsg, 'app_building_edit', ['id' => $building->getId()]);
        if ($response instanceof RedirectResponse) {
            $this->addFlash('success', $successMsg);

            return $response;
        }

        return $response;
    }
}
