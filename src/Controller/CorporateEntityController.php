<?php

namespace App\Controller;

use App\Controller\Traits\MunicipalityTrait;
use App\DTO\Paginator;
use App\Entity\CorporateEntity;
use App\Entity\Municipality;
use App\Form\CorporateEntityType;
use App\Repository\CorporateEntityRepository;
use App\Service\CrudActionService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

#[Route('/corporate/entity')]
#[IsGranted('ROLE_DRAFTSMAN')]
final class CorporateEntityController extends AbstractController
{
    use MunicipalityTrait;

    #[Route(name: 'app_corporate_entity_index', methods: ['GET'])]
    public function index(Request $request, CorporateEntityRepository $corporateEntityRepository): Response
    {
        $filter = $request->query->get('filter', '');
        $amountPerPage = $request->query->get('amount', 10);
        $pageNumber = $request->query->get('page', 1);

        $type = $request->query->get('entity', '');

        $data = $corporateEntityRepository->findEntities($filter, $amountPerPage, $pageNumber, $type);

        $template = ($request->isXmlHttpRequest()) ? '_list.html.twig' : 'index.html.twig';

        return $this->render("corporate_entity/$template", [
            'filter' => $filter,
            'paginator' => new Paginator($data, $amountPerPage, $pageNumber),
            'types' => \App\Entity\Enums\CorporateEntityType::cases()
        ]);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/new', name: 'app_corporate_entity_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CrudActionService $crudActionService): Response
    {
        $corporateEntity = new CorporateEntity();
        return $crudActionService->formLiveComponentAction($request, $corporateEntity, 'corporate_entity', [
            'title' => 'Nueva entidad corporativa',
            'ajax' => $request->isXmlHttpRequest()
        ]);
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    #[Route('/{id}', name: 'app_corporate_entity_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(Request $request, CorporateEntity $corporateEntity, CrudActionService $crudActionService): Response
    {
        return $crudActionService->showAction($request, $corporateEntity, 'corporate_entity', 'corporate_entity', 'Detalles de la entidad');
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/{id}/edit', name: 'app_corporate_entity_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function edit(Request $request, CorporateEntity $corporateEntity, CrudActionService $crudActionService, EntityManagerInterface $entityManager): Response
    {
        return $crudActionService->formLiveComponentAction($request, $corporateEntity, 'corporate_entity', [
            'title' => 'Editar entidad corporativa',
            'ajax' => $request->isXmlHttpRequest()
        ]);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/{id}', name: 'app_corporate_entity_delete', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function delete(Request $request, CorporateEntity $corporateEntity, CorporateEntityRepository $corporateEntityRepository, CrudActionService $crudActionService): Response
    {
        $successMsg = 'Se ha eliminado la entidad.';
        return $crudActionService->deleteAction($request, $corporateEntityRepository, $corporateEntity, $successMsg, 'app_corporate_entity_index');
    }
}
