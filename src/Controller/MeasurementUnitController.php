<?php

namespace App\Controller;

use App\Entity\MeasurementUnit;
use App\Repository\MeasurementUnitRepository;
use App\Service\CrudActionService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

#[Route('/measurement/unit')]
final class MeasurementUnitController extends AbstractController
{
    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route(name: 'app_measurement_unit_index', methods: ['GET'])]
    public function index(Request $request, MeasurementUnitRepository $measurementUnitRepository, CrudActionService $crudActionService): Response
    {
        return $crudActionService->indexAction($request, $measurementUnitRepository, 'findUnits', 'measurement_unit');
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/new', name: 'app_measurement_unit_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CrudActionService $crudActionService): Response
    {
        $measurementUnit = new MeasurementUnit();

        return $crudActionService->formLiveComponentAction($request, $measurementUnit, 'measurement_unit', [
            'title' => 'Nueva unidad de medida',
        ]);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/{id}', name: 'app_measurement_unit_show', methods: ['GET'])]
    public function show(Request $request, MeasurementUnit $measurementUnit, CrudActionService $crudActionService): Response
    {
        return $crudActionService->showAction($request, $measurementUnit, 'measurement_unit', 'measurement_unit', 'Detalles de la unidad de medida');
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/{id}/edit', name: 'app_measurement_unit_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, MeasurementUnit $measurementUnit, CrudActionService $crudActionService): Response
    {
        return $crudActionService->formLiveComponentAction($request, $measurementUnit, 'measurement_unit', [
            'title' => 'Editar unidad de medida',
        ]);
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    #[Route('/{id}', name: 'app_measurement_unit_delete', methods: ['POST'])]
    public function delete(Request $request, MeasurementUnit $measurementUnit, MeasurementUnitRepository $measurementUnitRepository, CrudActionService $crudActionService): Response
    {
        $successMsg = 'Se ha eliminado la unidad de medida.';
        $response = $crudActionService->deleteAction($request, $measurementUnitRepository, $measurementUnit, $successMsg, 'app_measurement_unit_index');
        if ($response instanceof RedirectResponse) {
            $this->addFlash('success', $successMsg);
        }

        return $response;
    }
}
