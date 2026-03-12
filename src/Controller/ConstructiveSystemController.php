<?php

namespace App\Controller;

use App\Entity\ConstructiveSystem;
use App\Form\ConstructiveSystemType;
use App\Repository\ConstructiveSystemRepository;
use App\Service\CrudActionService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

#[Route('/constructive/system')]
final class ConstructiveSystemController extends AbstractController
{
    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    #[Route(name: 'app_constructive_system_index', methods: ['GET'])]
    public function index(Request $request, ConstructiveSystemRepository $constructiveSystemRepository, CrudActionService $crudActionService): Response
    {
        return $crudActionService->indexAction($request, $constructiveSystemRepository, 'findConstructiveSystems', 'constructive_system');
    }

    #[Route('/new', name: 'app_constructive_system_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $constructiveSystem = new ConstructiveSystem();
        $form = $this->createForm(ConstructiveSystemType::class, $constructiveSystem);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($constructiveSystem);
            $entityManager->flush();

            return $this->redirectToRoute('app_constructive_system_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('constructive_system/new.html.twig', [
            'constructive_system' => $constructiveSystem,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_constructive_system_show', methods: ['GET'])]
    public function show(ConstructiveSystem $constructiveSystem): Response
    {
        return $this->render('constructive_system/show.html.twig', [
            'constructive_system' => $constructiveSystem,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_constructive_system_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ConstructiveSystem $constructiveSystem, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ConstructiveSystemType::class, $constructiveSystem);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_constructive_system_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('constructive_system/edit.html.twig', [
            'constructive_system' => $constructiveSystem,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_constructive_system_delete', methods: ['POST'])]
    public function delete(Request $request, ConstructiveSystem $constructiveSystem, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$constructiveSystem->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($constructiveSystem);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_constructive_system_index', [], Response::HTTP_SEE_OTHER);
    }
}
