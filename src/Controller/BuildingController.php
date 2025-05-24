<?php

namespace App\Controller;

use App\Entity\Building;
use App\Entity\Role;
use App\Form\BuildingType;
use App\Repository\BuildingRepository;
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
#[Route('/building')]
final class BuildingController extends AbstractController
{
    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route(name: 'app_building_index', methods: ['GET'])]
    public function index(Request $request, BuildingRepository $buildingRepository, CrudActionService $crudActionService): Response
    {
        return $crudActionService->indexAction($request, $buildingRepository, 'findBuildings', 'building');
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    #[Route('/new', name: 'app_building_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CrudActionService $crudActionService): Response
    {
        $building = new Building();
        return $crudActionService->formLiveComponentAction($request, $building, 'building', [
            'title' => 'Nueva Obra',
//            'ajax' => $request->isXmlHttpRequest()
        ]);
    }

    #[Route('/{id}', name: 'app_building_show', methods: ['GET'])]
    public function show(Building $building): Response
    {
        return $this->render('building/show.html.twig', [
            'building' => $building,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_building_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Building $building, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(BuildingType::class, $building);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_building_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('building/edit.html.twig', [
            'building' => $building,
            'form' => $form,
        ]);
    }

    #[IsGranted(Role::ROLE_ADMIN)]
    #[Route('/{id}', name: 'app_building_delete', methods: ['POST'])]
    public function delete(Request $request, Building $building, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$building->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($building);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_building_index', [], Response::HTTP_SEE_OTHER);
    }
}
