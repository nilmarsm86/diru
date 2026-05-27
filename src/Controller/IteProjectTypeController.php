<?php

namespace App\Controller;

use App\Entity\IteProjectType;
use App\Form\IteProjectTypeType;
use App\Repository\IteProjectTypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/ite/project/type')]
final class IteProjectTypeController extends AbstractController
{
    #[Route(name: 'app_ite_project_type_index', methods: ['GET'])]
    public function index(IteProjectTypeRepository $iteProjectTypeRepository): Response
    {
        return $this->render('ite_project_type/index.html.twig', [
            'ite_project_types' => $iteProjectTypeRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_ite_project_type_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $iteProjectType = new IteProjectType();
        $form = $this->createForm(IteProjectTypeType::class, $iteProjectType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($iteProjectType);
            $entityManager->flush();

            return $this->redirectToRoute('app_ite_project_type_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('ite_project_type/new.html.twig', [
            'ite_project_type' => $iteProjectType,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_ite_project_type_show', methods: ['GET'])]
    public function show(IteProjectType $iteProjectType): Response
    {
        return $this->render('ite_project_type/show.html.twig', [
            'ite_project_type' => $iteProjectType,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_ite_project_type_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, IteProjectType $iteProjectType, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(IteProjectTypeType::class, $iteProjectType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_ite_project_type_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('ite_project_type/edit.html.twig', [
            'ite_project_type' => $iteProjectType,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_ite_project_type_delete', methods: ['POST'])]
    public function delete(Request $request, IteProjectType $iteProjectType, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$iteProjectType->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($iteProjectType);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_ite_project_type_index', [], Response::HTTP_SEE_OTHER);
    }
}
