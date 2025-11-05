<?php

namespace App\Controller;

use App\Entity\Building;
use App\Entity\Organism;
use App\Entity\Role;
use App\Entity\UrbanizationEstimate;
use App\Form\UrbanizationEstimateType;
use App\Repository\OrganismRepository;
use App\Repository\UrbanizationEstimateRepository;
use App\Service\CrudActionService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

#[IsGranted(Role::ROLE_DRAFTSMAN)]
#[Route('/urbanization/estimate')]
final class UrbanizationEstimateController extends AbstractController
{
    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route(name: 'app_urbanization_estimate_index', methods: ['GET'])]
    public function index(Request $request, UrbanizationEstimateRepository $urbanizationEstimateRepository, CrudActionService $crudActionService): Response
    {
        return $crudActionService->indexAction($request, $urbanizationEstimateRepository, 'findUrbanizationEstimates', 'urbanization_estimate');
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    #[Route('/new/{building}', name: 'app_urbanization_estimate_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CrudActionService $crudActionService, Building $building = null): Response
    {
        $organism = new UrbanizationEstimate();
        return $crudActionService->formLiveComponentAction($request, $organism, 'urbanization_estimate', [
            'title' => 'Nuevo estimado de urbanización',
            'building' => $building
        ]);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/{id}', name: 'app_urbanization_estimate_show', methods: ['GET'])]
    public function show(Request $request, UrbanizationEstimate $urbanizationEstimate, CrudActionService $crudActionService): Response
    {
        return $crudActionService->showAction($request, $urbanizationEstimate, 'urbanization_estimate', 'urbanization_estimate', 'Detalles del estimado de urbanización');
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    #[Route('/{id}/edit', name: 'app_urbanization_estimate_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, UrbanizationEstimate $urbanizationEstimate, CrudActionService $crudActionService): Response
    {
        return $crudActionService->formLiveComponentAction($request, $urbanizationEstimate, 'urbanization_estimate', [
            'title' => 'Editar estimado de urbanización',
        ]);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/{id}', name: 'app_urbanization_estimate_delete', methods: ['POST'])]
    public function delete(Request $request, UrbanizationEstimate $urbanizationEstimate, UrbanizationEstimateRepository $urbanizationEstimateRepository, CrudActionService $crudActionService): Response
    {
        $successMsg = 'Se ha eliminado el estimado de urbanización.';
        return $crudActionService->deleteAction($request, $urbanizationEstimateRepository, $urbanizationEstimate, $successMsg, 'app_urbanization_estimate_index');
    }
}
