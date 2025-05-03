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

    #[Route('/new', name: 'app_individual_client_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, IndividualClientRepository $individualClientRepository): Response
    {
        $dataAddress = (!empty($request->request->all())) ? $request->request->all()['individual_client']['streetAddress'] : null;

        $individualClient = new IndividualClient();
        $successMsg = 'Se ha agregado un cliente individual.';
        $form = $this->createForm(IndividualClientType::class, $individualClient, [
            'action' => $this->generateUrl('app_individual_client_new'),
            'province' => (!is_null($dataAddress)) ? (isset($dataAddress['address']['province']) ? (int) $dataAddress['address']['province'] : 0) : 0,
            'municipality' => (!is_null($dataAddress)) ? (isset($dataAddress['address']['municipality']) ? (int) $dataAddress['address']['municipality'] : 0) : 0,
            'street' => (!is_null($dataAddress)) ? (isset($dataAddress['street']) ? $dataAddress['street'] : '') : '',
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $municipalityId = $dataAddress['address']['municipality'] ?? null;
            $municipality = $this->findMunicipality($entityManager, $municipalityId);
            $individualClient->setMunicipality($municipality);

            $street = $dataAddress['street'] ?? null;
            $individualClient->setAddress($street);

            $individualClientRepository->save($individualClient, true);

            //si el formulario se mando correctamente por ajax
            if ($request->isXmlHttpRequest()) {
                return $this->render("partials/_form_success.html.twig", [
                    'id' => 'new_individual_client' . $individualClient->getId(),
                    'type' => 'text-bg-success',
                    'message' => $successMsg
                ]);
            }

            $this->addFlash('success', $successMsg);
            return $this->redirectToRoute('app_individual_client_index', [], Response::HTTP_SEE_OTHER);
        }

        $template = ($request->isXmlHttpRequest()) ? '_form.html.twig' : 'new.html.twig';
        return $this->render("individual_client/$template", [
            'individual_client' => $individualClient,
            'form' => $form,
            'title' => 'Nueva persona natural',
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
//        return $this->render('individual_client/show.html.twig', [
//            'individual_client' => $individualClient,
//        ]);
        return $crudActionService->showAction($request, $individualClient, 'individual_client', 'individual_client', 'Detalles del cliente');
    }

    #[Route('/{id}/edit', name: 'app_individual_client_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, IndividualClient $individualClient, IndividualClientRepository $individualClientRepository, EntityManagerInterface $entityManager): Response
    {
        $dataAddress = (!empty($request->request->all())) ? $request->request->all()['individual_client']['streetAddress']['address'] : null;

        $provinceId = isset($dataAddress['province'])
            ? $dataAddress['province']
            : $individualClient->getMunicipality()->getProvince()->getId();

        $municipalityId = $this->getMunicipalityId($individualClient, $request->request->all()['individual_client']['streetAddress']);

        $successMsg = 'Se ha modificado el cliente individual.';

        $form = $this->createForm(IndividualClientType::class, $individualClient, [
            'action' => $this->generateUrl('app_individual_client_edit', ['id'=>$individualClient->getId()]),
            'crud' => true,
            'province' => (int) $provinceId,
            'municipality' => (int) $municipalityId,
//            'municipality' => (!is_null($dataAddress)) ? (isset($dataAddress['municipality']) ? (int) $dataAddress['municipality'] : 0) : 0,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $municipalityId = $dataAddress['municipality'] ?? null;
            $municipality = $this->findMunicipality($entityManager, $municipalityId);

            $individualClient->setMunicipality($municipality);
            $individualClientRepository->save($individualClient, true);

            //si el formulario se mando correctamente por ajax
            if ($request->isXmlHttpRequest()) {
                return $this->render("partials/_form_success.html.twig", [
                    'id' => 'edit_corporate_entity_' . $individualClient->getId(),
                    'type' => 'text-bg-success',
                    'message' => $successMsg
                ]);
            }

            $this->addFlash('success', $successMsg);
            return $this->redirectToRoute('app_individual_client_index', [], Response::HTTP_SEE_OTHER);
        }

        $template = ($request->isXmlHttpRequest()) ? '_form.html.twig' : 'edit.html.twig';
        return $this->render("individual_client/$template", [
            'individual_client' => $individualClient,
            'form' => $form,
            'title' => 'Editar cliente individual',
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
