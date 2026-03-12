<?php

namespace App\Controller;

use App\DTO\EnumSimulator;
use App\DTO\Paginator;
use App\Entity\Enums\UrbanRegulationStructure;
use App\Entity\Role;
use App\Entity\UrbanRegulation;
use App\Repository\UrbanRegulationRepository;
use App\Repository\UrbanRegulationTypeRepository;
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
#[Route('/urban/regulation')]
final class UrbanRegulationController extends AbstractController
{
    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route(name: 'app_urban_regulation_index', methods: ['GET'])]
    public function index(Request $request, RouterInterface $router, UrbanRegulationRepository $urbanRegulationRepository, UrbanRegulationTypeRepository $urbanRegulationTypeRepository): Response
    {
        $filter = $request->query->get('filter', '');
        $amountPerPage = (int) $request->query->get('amount', '10');
        $pageNumber = (int) $request->query->get('page', '1');

        $type = $request->query->get('type', '');
        $structure = $request->query->get('structure', '');

        $data = $urbanRegulationRepository->findUrbanRegulations($filter, $amountPerPage, $pageNumber, $type, $structure);

        $paginator = new Paginator($data, $amountPerPage, $pageNumber);
        if ($paginator->isFromGreaterThanTotal()) {
            return $paginator->greatherThanTotal($request, $router, $pageNumber);
        }

        $enumSimulators = [];
        foreach ($urbanRegulationTypeRepository->findAll() as $urbanRegulationType) {
            $enumSimulators[] = new EnumSimulator($urbanRegulationType->getName());
        }

        $template = ($request->isXmlHttpRequest()) ? '_list.html.twig' : 'index.html.twig';

        return $this->render("urban_regulation/$template", [
            'filter' => $filter,
            'paginator' => $paginator,
            'types' => $enumSimulators,
            'structures' => UrbanRegulationStructure::cases(),
        ]);
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    #[Route('/new', name: 'app_urban_regulation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CrudActionService $crudActionService): Response
    {
        $urbanRegulation = new UrbanRegulation();

        return $crudActionService->formLiveComponentAction($request, $urbanRegulation, 'urban_regulation', [
            'title' => 'Nueva regulación urbana',
        ]);
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    #[Route('/{id}', name: 'app_urban_regulation_show', methods: ['GET'])]
    public function show(Request $request, UrbanRegulation $urbanRegulation, CrudActionService $crudActionService): Response
    {
        return $crudActionService->showAction($request, $urbanRegulation, 'urban_regulation', 'urban_regulation', 'Detalles de la regulación urbana');
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/{id}/edit', name: 'app_urban_regulation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, UrbanRegulation $urbanRegulation, CrudActionService $crudActionService): Response
    {
        return $crudActionService->formLiveComponentAction($request, $urbanRegulation, 'urban_regulation', [
            'title' => 'Editar regulación urbana',
        ]);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/{id}', name: 'app_urban_regulation_delete', methods: ['POST'])]
    public function delete(Request $request, UrbanRegulation $urbanRegulation, UrbanRegulationRepository $urbanRegulationRepository, CrudActionService $crudActionService): Response
    {
        $successMsg = 'Se ha eliminado la regulación urbana.';
        $response = $crudActionService->deleteAction($request, $urbanRegulationRepository, $urbanRegulation, $successMsg, 'app_urban_regulation_index');
        if ($response instanceof RedirectResponse) {
            $this->addFlash('success', $successMsg);

            return $response;
        }

        return $response;
    }
}
