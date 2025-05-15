<?php

namespace App\Controller;

use App\Entity\LocationZone;
use App\Form\LocationZoneType;
use App\Repository\LocationZoneRepository;
use App\Service\CrudActionService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

#[Route('/location/zone')]
final class LocationZoneController extends AbstractController
{
    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route(name: 'app_location_zone_index', methods: ['GET'])]
    public function index(Request $request, LocationZoneRepository $locationZoneRepository, CrudActionService $crudActionService): Response
    {
        return $crudActionService->indexAction($request, $locationZoneRepository, 'findLocationZones', 'location_zone');
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/new', name: 'app_location_zone_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CrudActionService $crudActionService): Response
    {
        $locationZone = new LocationZone();
        return $crudActionService->formLiveComponentAction($request, $locationZone, 'location_zone', [
            'title' => 'Nueva zona de ubicación',
            'ajax' => $request->isXmlHttpRequest()
        ]);
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    #[Route('/{id}', name: 'app_location_zone_show', methods: ['GET'])]
    public function show(Request $request, LocationZone $locationZone, CrudActionService $crudActionService): Response
    {
        return $crudActionService->showAction($request, $locationZone, 'location_zone', 'location_zone', 'Detalles del zona de ubicación');
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    #[Route('/{id}/edit', name: 'app_location_zone_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, LocationZone $locationZone, CrudActionService $crudActionService): Response
    {
        return $crudActionService->formLiveComponentAction($request, $locationZone, 'location_zone', [
            'title' => 'Editar zona de ubicación',
            'ajax' => $request->isXmlHttpRequest()
        ]);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/{id}', name: 'app_location_zone_delete', methods: ['POST'])]
    public function delete(Request $request, LocationZone $locationZone, LocationZoneRepository $locationZoneRepository, CrudActionService $crudActionService): Response
    {
        $successMsg = 'Se ha eliminado la zona de ubicación.';
        return $crudActionService->deleteAction($request, $locationZoneRepository, $locationZone, $successMsg, 'app_location_zone_index');
    }
}
