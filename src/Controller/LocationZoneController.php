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
//        $form = $this->createForm(LocationZoneType::class, $locationZone);
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//            $entityManager->persist($locationZone);
//            $entityManager->flush();
//
//            return $this->redirectToRoute('app_location_zone_index', [], Response::HTTP_SEE_OTHER);
//        }
//
//        return $this->render('location_zone/new.html.twig', [
//            'location_zone' => $locationZone,
//            'form' => $form,
//        ]);
        return $crudActionService->formLiveComponentAction($request, $locationZone, 'location_zone', [
            'title' => 'Nueva zona de ubicación',
            'ajax' => $request->isXmlHttpRequest()
        ]);
    }

    #[Route('/{id}', name: 'app_location_zone_show', methods: ['GET'])]
    public function show(LocationZone $locationZone): Response
    {
        return $this->render('location_zone/show.html.twig', [
            'location_zone' => $locationZone,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_location_zone_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, LocationZone $locationZone, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(LocationZoneType::class, $locationZone);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_location_zone_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('location_zone/edit.html.twig', [
            'location_zone' => $locationZone,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_location_zone_delete', methods: ['POST'])]
    public function delete(Request $request, LocationZone $locationZone, LocationZoneRepository $locationZoneRepository, CrudActionService $crudActionService): Response
    {
        $successMsg = 'Se ha eliminado la entidad.';
        return $crudActionService->deleteAction($request, $locationZoneRepository, $crudActionService, $successMsg, 'app_corporate_entity_index');
    }
}
