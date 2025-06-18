<?php

namespace App\Controller;

use App\Entity\Floor;
use App\Form\FloorType;
use App\Repository\FloorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/floor')]
final class FloorController extends AbstractController
{
    #[Route(name: 'app_floor_index', methods: ['GET'])]
    public function index(FloorRepository $floorRepository): Response
    {
        return $this->render('floor/index.html.twig', [
            'floors' => $floorRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_floor_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $floor = new Floor();
        $form = $this->createForm(FloorType::class, $floor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($floor);
            $entityManager->flush();

            return $this->redirectToRoute('app_floor_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('floor/new.html.twig', [
            'floor' => $floor,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_floor_show', methods: ['GET'])]
    public function show(Floor $floor): Response
    {
        return $this->render('floor/show.html.twig', [
            'floor' => $floor,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_floor_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Floor $floor, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(FloorType::class, $floor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_floor_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('floor/edit.html.twig', [
            'floor' => $floor,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_floor_delete', methods: ['POST'])]
    public function delete(Request $request, Floor $floor, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$floor->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($floor);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_floor_index', [], Response::HTTP_SEE_OTHER);
    }
}
