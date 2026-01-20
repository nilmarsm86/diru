<?php

namespace App\Controller;

use App\Entity\BuildingRevision;
use App\Form\BuildingRevisionType;
use App\Repository\BuildingRevisionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/building/revision')]
final class BuildingRevisionController extends AbstractController
{
    #[Route(name: 'app_building_revision_index', methods: ['GET'])]
    public function index(BuildingRevisionRepository $buildingRevisionRepository): Response
    {
        return $this->render('building_revision/index.html.twig', [
            'building_revisions' => $buildingRevisionRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_building_revision_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $buildingRevision = new BuildingRevision();
        $form = $this->createForm(BuildingRevisionType::class, $buildingRevision);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($buildingRevision);
            $entityManager->flush();

            return $this->redirectToRoute('app_building_revision_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('building_revision/new.html.twig', [
            'building_revision' => $buildingRevision,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_building_revision_show', methods: ['GET'])]
    public function show(BuildingRevision $buildingRevision): Response
    {
        return $this->render('building_revision/show.html.twig', [
            'building_revision' => $buildingRevision,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_building_revision_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, BuildingRevision $buildingRevision, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(BuildingRevisionType::class, $buildingRevision);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_building_revision_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('building_revision/edit.html.twig', [
            'building_revision' => $buildingRevision,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_building_revision_delete', methods: ['POST'])]
    public function delete(Request $request, BuildingRevision $buildingRevision, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$buildingRevision->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($buildingRevision);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_building_revision_index', [], Response::HTTP_SEE_OTHER);
    }
}
