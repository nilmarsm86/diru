<?php

namespace App\Controller;

use App\Entity\SeparateConcept;
use App\Form\SeparateConceptType;
use App\Repository\SeparateConceptRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/separate/concept')]
final class SeparateConceptController extends AbstractController
{
    #[Route(name: 'app_separate_concept_index', methods: ['GET'])]
    public function index(SeparateConceptRepository $separateConceptRepository): Response
    {
        return $this->render('separate_concept/index.html.twig', [
            'separate_concepts' => $separateConceptRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_separate_concept_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $separateConcept = new SeparateConcept();
        $form = $this->createForm(SeparateConceptType::class, $separateConcept);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($separateConcept);
            $entityManager->flush();

            return $this->redirectToRoute('app_separate_concept_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('separate_concept/new.html.twig', [
            'separate_concept' => $separateConcept,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_separate_concept_show', methods: ['GET'])]
    public function show(SeparateConcept $separateConcept): Response
    {
        return $this->render('separate_concept/show.html.twig', [
            'separate_concept' => $separateConcept,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_separate_concept_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, SeparateConcept $separateConcept, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SeparateConceptType::class, $separateConcept);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_separate_concept_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('separate_concept/edit.html.twig', [
            'separate_concept' => $separateConcept,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_separate_concept_delete', methods: ['POST'])]
    public function delete(Request $request, SeparateConcept $separateConcept, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$separateConcept->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($separateConcept);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_separate_concept_index', [], Response::HTTP_SEE_OTHER);
    }
}
