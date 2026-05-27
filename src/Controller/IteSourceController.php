<?php

namespace App\Controller;

use App\Entity\IteSource;
use App\Form\IteSourceType;
use App\Repository\IteSourceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/ite/source')]
final class IteSourceController extends AbstractController
{
    #[Route(name: 'app_ite_source_index', methods: ['GET'])]
    public function index(IteSourceRepository $iteSourceRepository): Response
    {
        return $this->render('ite_source/index.html.twig', [
            'ite_sources' => $iteSourceRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_ite_source_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $iteSource = new IteSource();
        $form = $this->createForm(IteSourceType::class, $iteSource);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($iteSource);
            $entityManager->flush();

            return $this->redirectToRoute('app_ite_source_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('ite_source/new.html.twig', [
            'ite_source' => $iteSource,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_ite_source_show', methods: ['GET'])]
    public function show(IteSource $iteSource): Response
    {
        return $this->render('ite_source/show.html.twig', [
            'ite_source' => $iteSource,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_ite_source_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, IteSource $iteSource, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(IteSourceType::class, $iteSource);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_ite_source_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('ite_source/edit.html.twig', [
            'ite_source' => $iteSource,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_ite_source_delete', methods: ['POST'])]
    public function delete(Request $request, IteSource $iteSource, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$iteSource->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($iteSource);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_ite_source_index', [], Response::HTTP_SEE_OTHER);
    }
}
