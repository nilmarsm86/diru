<?php

namespace App\Controller;

use App\DTO\Paginator;
use App\Entity\Building;
use App\Entity\Role;
use App\Entity\UrbanizationEstimate;
use App\Repository\UrbanizationEstimateRepository;
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
#[Route('/urbanization/estimate')]
final class UrbanizationEstimateController extends AbstractController
{
    #[Route('/{building}', name: 'app_urbanization_estimate_index', methods: ['GET'])]
    public function index(
        Request $request,
        RouterInterface $router,
        UrbanizationEstimateRepository $urbanizationEstimateRepository,
        Building $building): Response
    {
        $filter = $request->query->get('filter', '');
        $amountPerPage = (int) $request->query->get('amount', '10');
        $pageNumber = (int) $request->query->get('page', '1');

        $data = $urbanizationEstimateRepository->findUrbanizationEstimates($building, $filter, $amountPerPage, $pageNumber);

        $paginator = new Paginator($data, $amountPerPage, $pageNumber);
        if ($paginator->isFromGreaterThanTotal()) {
            return $paginator->greatherThanTotal($request, $router, $pageNumber);
        }

        $template = ($request->isXmlHttpRequest()) ? '_list.html.twig' : 'index.html.twig';

        return $this->render("urbanization_estimate/$template", [
            'filter' => $filter,
            'paginator' => $paginator,
            'building' => $building->getId(),
        ]);
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    #[Route('/new/{building}', name: 'app_urbanization_estimate_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CrudActionService $crudActionService, ?Building $building = null): Response
    {
        $urbanizationEstimate = new UrbanizationEstimate();

        return $crudActionService->formLiveComponentAction($request, $urbanizationEstimate, 'urbanization_estimate', [
            'title' => 'Nuevo estimado de urbanización',
            'building' => $building,
        ]);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/{id}/{building}', name: 'app_urbanization_estimate_show', methods: ['GET'])]
    public function show(Request $request, UrbanizationEstimate $urbanizationEstimate, CrudActionService $crudActionService, ?Building $building = null): Response
    {
        return $crudActionService->showAction($request, $urbanizationEstimate, 'urbanization_estimate', 'urbanization_estimate', 'Detalles del estimado de urbanización', [
            'building' => $building,
        ]);
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    #[Route('/{id}/edit/{building}', name: 'app_urbanization_estimate_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, UrbanizationEstimate $urbanizationEstimate, CrudActionService $crudActionService, ?Building $building = null): Response
    {
        return $crudActionService->formLiveComponentAction($request, $urbanizationEstimate, 'urbanization_estimate', [
            'title' => 'Editar estimado de urbanización',
            'building' => $building,
        ]);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/{id}/{building}', name: 'app_urbanization_estimate_delete', methods: ['POST'])]
    public function delete(Request $request, UrbanizationEstimate $urbanizationEstimate, UrbanizationEstimateRepository $urbanizationEstimateRepository, CrudActionService $crudActionService, Building $building): Response
    {
        $successMsg = 'Se ha eliminado el estimado de urbanización.';
        $response = $crudActionService->deleteAction($request, $urbanizationEstimateRepository, $urbanizationEstimate, $successMsg, 'app_building_edit', [
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
