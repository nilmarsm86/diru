<?php

namespace App\Controller;

use App\Controller\Traits\PdfResponseTrait;
use App\DTO\Paginator;
use App\Entity\CorporateEntity;
use App\Entity\Enums\CorporateEntityType;
use App\Entity\Role;
use App\Repository\CorporateEntityRepository;
use App\Service\CrudActionService;
use App\Service\Pdf\PdfAssetManager;
use App\Service\Pdf\PdfGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

#[IsGranted(Role::ROLE_DRAFTSMAN)]
#[Route('/corporate/entity')]
final class CorporateEntityController extends AbstractController
{
    use PdfResponseTrait;

    #[Route(name: 'app_corporate_entity_index', methods: ['GET'])]
    public function index(Request $request, RouterInterface $router, CorporateEntityRepository $corporateEntityRepository): Response
    {
        $filter = $request->query->get('filter', '');
        $amountPerPage = (int) $request->query->get('amount', '10');
        $pageNumber = (int) $request->query->get('page', '1');

        $type = $request->query->get('entity', '');

        $data = $corporateEntityRepository->findEntities($filter, $amountPerPage, $pageNumber, $type);

        $paginator = new Paginator($data, $amountPerPage, $pageNumber);
        if ($paginator->isFromGreaterThanTotal()) {
            return $paginator->greatherThanTotal($request, $router, $pageNumber);
        }

        $template = ($request->isXmlHttpRequest()) ? '_list.html.twig' : 'index.html.twig';

        return $this->render("corporate_entity/$template", [
            'filter' => $filter,
            'paginator' => $paginator,
            'types' => CorporateEntityType::cases(),
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
        $response = $crudActionService->deleteAction($request, $corporateEntityRepository, $corporateEntity, $successMsg, 'app_corporate_entity_index');
        if ($response instanceof RedirectResponse) {
            $this->addFlash('success', $successMsg);

            return $response;
        }

        return $response;
    }

    #[Route('/print', name: 'app_corporate_entity_print', methods: ['GET'])]
    public function print(Request $request, CorporateEntityRepository $corporateEntityRepository, PdfAssetManager $pdfAssetManager, PdfGenerator $pdfGenerator): Response
    {
        $filter = $request->query->get('filter', '');
        $type = $request->query->get('entity', '');
        $data = $corporateEntityRepository->findEntities($filter, null, null, $type);

        $paginator = new Paginator($data);

        return $this->renderPdf($filter, $paginator, $pdfAssetManager, $pdfGenerator, 'corporate_entity/pdf/print.html.twig', 'Listado de entidades corporativas', 'entidades');
    }

    #[Route('/amount_corporate_entity_type_report', name: 'app_corporate_entity_type_report', methods: ['GET'])]
    public function amountTypeReport(Request $request, RouterInterface $router, CorporateEntityRepository $corporateEntityRepository): Response
    {
        $filter = $request->query->get('filter', '');
        $amountPerPage = (int) $request->query->get('amount', '10');
        $pageNumber = (int) $request->query->get('page', '1');

        $data = $corporateEntityRepository->findByType($filter, $amountPerPage, $pageNumber);
        $paginator = new Paginator($data, $amountPerPage, $pageNumber);
        if ($paginator->isFromGreaterThanTotal()) {
            return $paginator->greatherThanTotal($request, $router, $pageNumber);
        }

        $template = ($request->isXmlHttpRequest()) ? '_amount_type.html.twig' : 'report.html.twig';

        return $this->render("corporate_entity/report/$template", [
            'filter' => $filter,
            'paginator' => $paginator,
            'title' => 'Cantidad de entidades corporativas por tipo',
            'list' => '_amount_type',
            'types' => CorporateEntityType::cases(),
        ]);
    }

    #[Route('/amount_corporate_entity_type_report_print', name: 'app_corporate_entity_type_report_print', methods: ['GET'])]
    public function amountTypeReportPrint(Request $request, CorporateEntityRepository $corporateEntityRepository, RouterInterface $router, PdfAssetManager $pdfAssetManager, PdfGenerator $pdfGenerator): Response
    {
        $filter = $request->query->get('filter', '');
        $amountPerPage = (int) $request->query->get('amount', '10');
        $pageNumber = (int) $request->query->get('page', '1');

        $data = $corporateEntityRepository->findByType($filter, null, null);
        $paginator = new Paginator($data, null, null);
        if ($paginator->isFromGreaterThanTotal()) {
            return $paginator->greatherThanTotal($request, $router, $pageNumber);
        }

        return $this->renderPdf(
            $filter,
            $paginator,
            $pdfAssetManager,
            $pdfGenerator,
            'corporate_entity/pdf/amount_type.twig',
            'Cantidad de entidades corporativas por tipo',
            'entidades_tipo'
        );
    }

    #[Route('/amount_project_building_report', name: 'app_corporate_entity_amount_project_building_report', methods: ['GET'])]
    public function amountProjectAndBuildingReport(Request $request, RouterInterface $router, CorporateEntityRepository $corporateEntityRepository): Response
    {
        $filter = $request->query->get('filter', '');
        $amountPerPage = (int) $request->query->get('amount', '10');
        $pageNumber = (int) $request->query->get('page', '1');

        $data = $corporateEntityRepository->findAmountProjectAndBuildings($filter, $amountPerPage, $pageNumber);
        $paginator = new Paginator($data, $amountPerPage, $pageNumber);
        if ($paginator->isFromGreaterThanTotal()) {
            return $paginator->greatherThanTotal($request, $router, $pageNumber);
        }

        $template = ($request->isXmlHttpRequest()) ? '_amount_project_building.html.twig' : 'report.html.twig';

        return $this->render("corporate_entity/report/$template", [
            'filter' => $filter,
            'paginator' => $paginator,
            'title' => 'Cantidad de proyectos y obras',
            'list' => '_amount_project_building',
        ]);
    }

    #[Route('/amount_project_building_report_print', name: 'app_corporate_entity_amount_project_building_report_print', methods: ['GET'])]
    public function amountProjectAndBuildingReportPrint(Request $request, CorporateEntityRepository $corporateEntityRepository, RouterInterface $router, PdfAssetManager $pdfAssetManager, PdfGenerator $pdfGenerator): Response
    {
        $filter = $request->query->get('filter', '');
        $amountPerPage = (int) $request->query->get('amount', '10');
        $pageNumber = (int) $request->query->get('page', '1');

        $data = $corporateEntityRepository->findAmountProjectAndBuildings($filter, null, null);
        $paginator = new Paginator($data, null, null);
        if ($paginator->isFromGreaterThanTotal()) {
            return $paginator->greatherThanTotal($request, $router, $pageNumber);
        }

        return $this->renderPdf(
            $filter,
            $paginator,
            $pdfAssetManager,
            $pdfGenerator,
            'corporate_entity/pdf/amount_project_building.twig',
            'Cantidad de proyectos y obras',
            'corporate_entity_proyectos_obras'
        );
    }
}
