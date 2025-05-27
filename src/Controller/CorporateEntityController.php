<?php

namespace App\Controller;

use App\Controller\Traits\MunicipalityTrait;
use App\DTO\Paginator;
use App\Entity\CorporateEntity;
use App\Entity\Enums\CorporateEntityType;
use App\Entity\Role;
use App\Repository\CorporateEntityRepository;
use App\Service\CrudActionService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

#[IsGranted(Role::ROLE_DRAFTSMAN)]
#[Route('/corporate/entity')]
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

        $paginator = new Paginator($data, $amountPerPage, $pageNumber);
        if ($paginator->isFromGreaterThanTotal()) {
            $number = ($pageNumber === 1) ? 1 : ($pageNumber - 1);
            return new RedirectResponse($this->generateUrl($request->attributes->get('_route'), [...$request->query->all(), 'page' => $number]), Response::HTTP_SEE_OTHER);
        }

        $template = ($request->isXmlHttpRequest()) ? '_list.html.twig' : 'index.html.twig';

        return $this->render("corporate_entity/$template", [
            'filter' => $filter,
            'paginator' => $paginator,
            'types' => CorporateEntityType::cases()
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
//            'ajax' => $request->isXmlHttpRequest()
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
    public function edit(Request $request, CorporateEntity $corporateEntity, CrudActionService $crudActionService): Response
    {
        return $crudActionService->formLiveComponentAction($request, $corporateEntity, 'corporate_entity', [
            'title' => 'Editar entidad corporativa',
//            'ajax' => $request->isXmlHttpRequest()
        ]);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[IsGranted(Role::ROLE_ADMIN)]
    #[Route('/{id}', name: 'app_corporate_entity_delete', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function delete(Request $request, CorporateEntity $corporateEntity, CorporateEntityRepository $corporateEntityRepository, CrudActionService $crudActionService): Response
    {
        $successMsg = 'Se ha eliminado la entidad.';
        return $crudActionService->deleteAction($request, $corporateEntityRepository, $corporateEntity, $successMsg, 'app_corporate_entity_index');
    }

//    #[Route('/options/{id}', name: 'app_corporate_entity_options', requirements: ['id' => '\d+'], methods: ['GET'])]
//    public function options(Request $request, CorporateEntity $corporateEntity, CorporateEntityRepository $corporateEntityRepository): Response
//    {
////        if ($request->isXmlHttpRequest()) {
////            return $this->render('partials/_select_options.html.twig', [
////                'entities' => $corporateEntityRepository->findBy([], ['name' => 'ASC']),
////                'selected' => $corporateEntity->getId()
////            ]);
////        }
//
//        throw new BadRequestHttpException('Ajax request');
//    }
}
