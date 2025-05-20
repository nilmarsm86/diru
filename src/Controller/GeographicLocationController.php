<?php

namespace App\Controller;

use App\Entity\GeographicLocation;
use App\Form\GeographicLocationType;
use App\Repository\GeographicLocationRepository;
use App\Service\CrudActionService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

#[Route('/geographic/location')]
final class GeographicLocationController extends AbstractController
{
    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route(name: 'app_geographic_location_index', methods: ['GET'])]
    public function index(Request $request, GeographicLocationRepository $geographicLocationRepository, CrudActionService $crudActionService): Response
    {
        return $crudActionService->indexAction($request, $geographicLocationRepository, 'findGeographicLocations', 'geographic_location');
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/new', name: 'app_geographic_location_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CrudActionService $crudActionService): Response
    {
        $geographicLocation = new GeographicLocation();
        return $crudActionService->formLiveComponentAction($request, $geographicLocation, 'geographic_location', [
            'title' => 'Nueva ubicación geográfica',
            'ajax' => $request->isXmlHttpRequest()
        ]);
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    #[Route('/{id}', name: 'app_geographic_location_show', methods: ['GET'])]
    public function show(Request $request, GeographicLocation $geographicLocation, CrudActionService $crudActionService): Response
    {
        return $crudActionService->showAction($request, $geographicLocation, 'geographic_location', 'geographic_location', 'Detalles de la ubicación geográfica');
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    #[Route('/{id}/edit', name: 'app_geographic_location_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, GeographicLocation $geographicLocation, CrudActionService $crudActionService): Response
    {
        return $crudActionService->formLiveComponentAction($request, $geographicLocation, 'geographic_location', [
            'title' => 'Editar ubicación geográfica',
            'ajax' => $request->isXmlHttpRequest()
        ]);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/{id}', name: 'app_geographic_location_delete', methods: ['POST'])]
    public function delete(Request $request, GeographicLocation $geographicLocation, GeographicLocationRepository $geographicLocationRepository, CrudActionService $crudActionService): Response
    {
        $successMsg = 'Se ha eliminado la ubicación geográfica.';
        return $crudActionService->deleteAction($request, $geographicLocationRepository, $geographicLocation, $successMsg, 'app_location_zone_index');
    }
}
