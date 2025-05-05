<?php

namespace App\Controller;

use App\Entity\EnterpriseClient;
use App\Form\EnterpriseClientType;
use App\Repository\EnterpriseClientRepository;
use App\Service\CrudActionService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

#[Route('/enterprise/client')]
final class EnterpriseClientController extends AbstractController
{
    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route(name: 'app_enterprise_client_index', methods: ['GET'])]
    public function index(Request $request, EnterpriseClientRepository $enterpriseClientRepository, CrudActionService $crudActionService): Response
    {
//        return $this->render('enterprise_client/index.html.twig', [
//            'enterprise_clients' => $enterpriseClientRepository->findAll(),
//        ]);
        return $crudActionService->indexAction($request, $enterpriseClientRepository, 'findEnterprises', 'enterprise_client');
    }

    #[Route('/new', name: 'app_enterprise_client_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $enterpriseClient = new EnterpriseClient();
        $form = $this->createForm(EnterpriseClientType::class, $enterpriseClient);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($enterpriseClient);
            $entityManager->flush();

            return $this->redirectToRoute('app_enterprise_client_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('enterprise_client/new.html.twig', [
            'enterprise_client' => $enterpriseClient,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_enterprise_client_show', methods: ['GET'])]
    public function show(EnterpriseClient $enterpriseClient): Response
    {
        return $this->render('enterprise_client/show.html.twig', [
            'enterprise_client' => $enterpriseClient,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_enterprise_client_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, EnterpriseClient $enterpriseClient, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EnterpriseClientType::class, $enterpriseClient);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_enterprise_client_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('enterprise_client/edit.html.twig', [
            'enterprise_client' => $enterpriseClient,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_enterprise_client_delete', methods: ['POST'])]
    public function delete(Request $request, EnterpriseClient $enterpriseClient, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$enterpriseClient->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($enterpriseClient);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_enterprise_client_index', [], Response::HTTP_SEE_OTHER);
    }
}
