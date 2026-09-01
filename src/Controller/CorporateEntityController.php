<?php

namespace App\Controller;

use App\Controller\Traits\PdfResponseTrait;
use App\DTO\Paginator;
use App\Entity\CorporateEntity;
use App\Entity\Enums\CorporateEntityType;
use App\Entity\Role;
use App\Repository\CorporateEntityRepository;
use App\Repository\EnterpriseClientRepository;
use App\Repository\ProjectRepository;
use App\Service\CrudActionService;
use App\Service\Pdf\PdfAssetManager;
use App\Service\Pdf\PdfGenerator;
use Doctrine\DBAL\Exception;
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

    /**
     * @throws Exception
     */
    #[Route('/amount_finance_report', name: 'app_corporate_entity_amount_finance_report', methods: ['GET'])]
    public function amountFinanceReport(Request $request,
        RouterInterface $router,
        CorporateEntityRepository $corporateEntityRepository,
        EnterpriseClientRepository $enterpriseClientRepository,
        ProjectRepository $projectRepository,
    ): Response {
        $response = $this->amountFinance($request, $router, $corporateEntityRepository, $enterpriseClientRepository, $projectRepository);
        if ($response instanceof RedirectResponse) {
            return $response;
        }
        [$filter, $paginator] = $response;

        $template = ($request->isXmlHttpRequest()) ? '_amount_finance.html.twig' : 'report.html.twig';

        return $this->render("corporate_entity/report/$template", [
            'filter' => $filter,
            'paginator' => $paginator,
            'title' => 'Finanzas de obras por entidades corporativas',
            'list' => '_amount_finance',
            'currency' => 'CUP', // TODO: poner la moneda del sistema
        ]);
    }

    /**
     * @throws Exception
     */
    #[Route('/amount_finance_report_print', name: 'app_corporate_entity_amount_finance_report_print', methods: ['GET'])]
    public function amountFinanceReportPrint(Request $request,
        CorporateEntityRepository $corporateEntityRepository,
        EnterpriseClientRepository $enterpriseClientRepository,
        ProjectRepository $projectRepository,
        RouterInterface $router,
        PdfAssetManager $pdfAssetManager,
        PdfGenerator $pdfGenerator,
    ): Response {
        $response = $this->amountFinance($request, $router, $corporateEntityRepository, $enterpriseClientRepository, $projectRepository, true);
        if ($response instanceof RedirectResponse) {
            return $response;
        }
        [$filter, $paginator] = $response;
        assert($paginator instanceof Paginator);

        return $this->renderPdf($filter, $paginator, $pdfAssetManager, $pdfGenerator, 'corporate_entity/pdf/amount_finance.twig', 'Finanzas de obras por entidades corporativas', 'corporate_entity_finanzas', ['currency' => 'CUP']);
    }

    /**
     * @return RedirectResponse|array<mixed>
     *
     * @throws Exception
     */
    private function amountFinance(
        Request $request,
        RouterInterface $router,
        CorporateEntityRepository $corporateEntityRepository,
        EnterpriseClientRepository $enterpriseClientRepository,
        ProjectRepository $projectRepository,
        bool $pdf = false,
    ): RedirectResponse|array {
        $filter = $request->query->get('filter', '');
        $amountPerPage = (int) $request->query->get('amount', '10');
        $pageNumber = (int) $request->query->get('page', '1');

        if (true === $pdf) {
            $amountPerPage = null;
            $pageNumber = null;
        }

        $data = $corporateEntityRepository->findEntities($filter, $amountPerPage, $pageNumber);
        $newData = $this->addFinance($enterpriseClientRepository, $projectRepository, $data);

        $paginator = new Paginator($newData, $amountPerPage, $pageNumber, count($corporateEntityRepository->findEntities($filter, null, null)));
        if ($paginator->isFromGreaterThanTotal()) {
            return $paginator->greatherThanTotal($request, $router, $pageNumber);
        }

        return [$filter, $paginator];
    }

    /**
     * @param \Doctrine\ORM\Tools\Pagination\Paginator<mixed> $data
     *
     * @return array<mixed>
     */
    public function addFinance(EnterpriseClientRepository $enterpriseClientRepository, ProjectRepository $projectRepository, \Doctrine\ORM\Tools\Pagination\Paginator $data): array
    {
        $newData = [];
        /* @var CorporateEntity $corporateEntity */
        foreach ($data as $corporateEntity) {
            assert($corporateEntity instanceof CorporateEntity);

            $item = [];
            $item['id'] = $corporateEntity->getId();
            $item['name'] = $corporateEntity->getName();

            $approvedValue = 0;
            $estimatedValue = 0;
            $estimatedAdjustValue = 0;
            $constructionAssembly = 0;
            $constructionRealValue = 0;

            $enterpriseClients = $enterpriseClientRepository->findBy(['corporateEntity' => $corporateEntity]);
            foreach ($enterpriseClients as $enterpriseClient) {
                $projects = $projectRepository->findBy(['client' => $enterpriseClient]);
                foreach ($projects as $project) {
                    foreach ($project->getBuildings() as $building) {
                        $approvedValue += (int) $building->getTotalApprovedValue();
                        $estimatedValue += $building->getPrice();
                        $estimatedAdjustValue += $building->getEstimatedAdjustValue();
                        $constructionAssembly += $building->getConstructionAssembly();
                        $constructionRealValue += $building->getConstructionRealValue();
                    }
                }
            }

            $item['approvedValue'] = $approvedValue;
            $item['estimatedValue'] = $estimatedValue;
            $item['estimatedAdjustValue'] = $estimatedAdjustValue;
            $item['constructionAssembly'] = $constructionAssembly;
            $item['constructionRealValue'] = $constructionRealValue;
            $newData[] = $item;
        }

        return $newData;
    }
}
