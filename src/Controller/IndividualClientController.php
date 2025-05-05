<?php

namespace App\Controller;

use App\Controller\Traits\MunicipalityTrait;
use App\Entity\IndividualClient;
use App\Form\IndividualClientType;
use App\Repository\IndividualClientRepository;
use App\Repository\MunicipalityRepository;
use App\Service\CrudActionService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

#[Route('/individual/client')]
final class IndividualClientController extends AbstractController
{
    use MunicipalityTrait;

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route(name: 'app_individual_client_index', methods: ['GET'])]
    public function index(Request $request, IndividualClientRepository $individualClientRepository, CrudActionService $crudActionService): Response
    {
        return $crudActionService->indexAction($request, $individualClientRepository, 'findIndividuals', 'individual_client');
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    #[Route('/new', name: 'app_individual_client_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CrudActionService $crudActionService): Response
    {
        $individualClient = new IndividualClient();
        return $crudActionService->formLiveComponentAction($request, $individualClient, 'individual_client', [
            'title' => 'Nueva persona natural',
            'ajax' => $request->isXmlHttpRequest()
        ]);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/{id}', name: 'app_individual_client_show', methods: ['GET'])]
    public function show(Request $request, IndividualClient $individualClient, CrudActionService $crudActionService): Response
    {
        return $crudActionService->showAction($request, $individualClient, 'individual_client', 'individual_client', 'Detalles del cliente');
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    #[Route('/{id}/edit', name: 'app_individual_client_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, IndividualClient $individualClient, CrudActionService $crudActionService): Response
    {
        return $crudActionService->formLiveComponentAction($request, $individualClient, 'individual_client', [
            'title' => 'Editar persona individual',
            'ajax' => $request->isXmlHttpRequest()
        ]);
    }

    #[Route('/{id}', name: 'app_individual_client_delete', methods: ['POST'])]
    public function delete(Request $request, IndividualClient $individualClient, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$individualClient->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($individualClient);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_individual_client_index', [], Response::HTTP_SEE_OTHER);
    }
}
