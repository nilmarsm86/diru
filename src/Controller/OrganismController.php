<?php

namespace App\Controller;

use App\Controller\Traits\PdfResponseTrait;
use App\DTO\Paginator;
use App\Entity\Investment;
use App\Entity\Organism;
use App\Entity\Role;
use App\Repository\CorporateEntityRepository;
use App\Repository\InvestmentRepository;
use App\Repository\OrganismRepository;
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
#[Route('/organism')]
final class OrganismController extends AbstractController
{
    use PdfResponseTrait;

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route(name: 'app_organism_index', methods: ['GET'])]
    public function index(Request $request, OrganismRepository $organismRepository, CrudActionService $crudActionService): Response
    {
        return $crudActionService->indexAction($request, $organismRepository, 'findOrganisms', 'organism');
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/new', name: 'app_organism_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CrudActionService $crudActionService): Response
    {
        $organism = new Organism();

        return $crudActionService->formLiveComponentAction($request, $organism, 'organism', [
            'title' => 'Nuevo organismo',
        ]);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/{id}', name: 'app_organism_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(Request $request, Organism $organism, CrudActionService $crudActionService): Response
    {
        return $crudActionService->showAction($request, $organism, 'organism', 'organism', 'Detalles del organismo');
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    #[Route('/{id}/edit', name: 'app_organism_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function edit(Request $request, Organism $organism, CrudActionService $crudActionService): Response
    {
        return $crudActionService->formLiveComponentAction($request, $organism, 'organism', [
            'title' => 'Editar organismo',
        ]);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[IsGranted(Role::ROLE_ADMIN)]
    #[Route('/{id}', name: 'app_organism_delete', requirements: ['id' => '\d+'], methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Request $request, Organism $organism, OrganismRepository $organismRepository, CrudActionService $crudActionService, CorporateEntityRepository $corporateEntityRepository): Response
    {
        $successMsg = 'Se ha eliminado el organismo.';
        $corporateEntities = $corporateEntityRepository->findBy(['organism' => $organism]);
        if (count($corporateEntities) > 0) {
            $template = [
                'id' => 'delete_organism_'.$organism->getId(),
                'type' => 'text-bg-danger',
                'message' => 'Este organismo esta relacionado con algunas entidades corportativas.',
            ];

            return new Response($this->renderView('partials/_form_success.html.twig', $template));
        }

        $response = $crudActionService->deleteAction($request, $organismRepository, $organism, $successMsg, 'app_organism_index');
        if ($response instanceof RedirectResponse) {
            $this->addFlash('success', $successMsg);

            return $response;
        }

        return $response;
    }

    #[Route('/print', name: 'app_organism_print', methods: ['GET'])]
    public function print(Request $request, OrganismRepository $organismRepository, PdfAssetManager $pdfAssetManager, PdfGenerator $pdfGenerator): Response
    {
        $filter = $request->query->get('filter', '');

        $data = $organismRepository->findOrganisms($filter, null, null);

        $paginator = new Paginator($data);

        return $this->renderPdf($filter, $paginator, $pdfAssetManager, $pdfGenerator, 'organism/pdf/print.html.twig', 'Listado de organismos', 'organismos');
    }

    #[Route('/amount_corporate_entity_report', name: 'app_organism_amount_corporate_entity_report', methods: ['GET'])]
    public function amountCorporateEntityReport(Request $request, RouterInterface $router, OrganismRepository $organismRepository): Response
    {
        $filter = $request->query->get('filter', '');
        $amountPerPage = (int) $request->query->get('amount', '10');
        $pageNumber = (int) $request->query->get('page', '1');

        $data = $organismRepository->findByCorporateEntityType($filter, $amountPerPage, $pageNumber);
        $paginator = new Paginator($data, $amountPerPage, $pageNumber);
        if ($paginator->isFromGreaterThanTotal()) {
            return $paginator->greatherThanTotal($request, $router, $pageNumber);
        }

        $template = ($request->isXmlHttpRequest()) ? '_amount_corporate_entity.html.twig' : 'report.html.twig';

        return $this->render("organism/report/$template", [
            'filter' => $filter,
            'paginator' => $paginator,
            'title' => 'Cantidad de entidades corporativas por tipo',
            'list' => '_amount_corporate_entity',
        ]);
    }

    #[Route('/amount_corporate_entity_report_print', name: 'app_organism_amount_corporate_entity_report_print', methods: ['GET'])]
    public function amountCorporateEntityReportPrint(Request $request, OrganismRepository $organismRepository, RouterInterface $router, PdfAssetManager $pdfAssetManager, PdfGenerator $pdfGenerator): Response
    {
        $filter = $request->query->get('filter', '');
        $amountPerPage = (int) $request->query->get('amount', '10');
        $pageNumber = (int) $request->query->get('page', '1');

        $data = $organismRepository->findByCorporateEntityType($filter, null, null);
        $paginator = new Paginator($data, null, null);
        if ($paginator->isFromGreaterThanTotal()) {
            return $paginator->greatherThanTotal($request, $router, $pageNumber);
        }

        return $this->renderPdf(
            $filter,
            $paginator,
            $pdfAssetManager,
            $pdfGenerator,
            'organism/pdf/amount_corporate_entity.twig',
            'Cantidad de entidades corporativas por tipo',
            'organismos_entidades_corporativas'
        );
    }

    #[Route('/amount_enterprise_client_report', name: 'app_organism_amount_enterprise_client_report', methods: ['GET'])]
    public function amountEnterpriseClientReport(Request $request, RouterInterface $router, OrganismRepository $organismRepository): Response
    {
        $filter = $request->query->get('filter', '');
        $amountPerPage = (int) $request->query->get('amount', '10');
        $pageNumber = (int) $request->query->get('page', '1');

        $data = $organismRepository->findByEnterpriseClient($filter, $amountPerPage, $pageNumber);
        $paginator = new Paginator($data, $amountPerPage, $pageNumber);
        if ($paginator->isFromGreaterThanTotal()) {
            return $paginator->greatherThanTotal($request, $router, $pageNumber);
        }

        $template = ($request->isXmlHttpRequest()) ? '_amount_enterprise_client.html.twig' : 'report.html.twig';

        return $this->render("organism/report/$template", [
            'filter' => $filter,
            'paginator' => $paginator,
            'title' => 'Cantidad de clientes empresariales',
            'list' => '_amount_enterprise_client',
        ]);
    }

    #[Route('/amount_enterprise_client_report_print', name: 'app_organism_amount_enterprise_client_report_print', methods: ['GET'])]
    public function amountEnterpriseClientReportPrint(Request $request, OrganismRepository $organismRepository, RouterInterface $router, PdfAssetManager $pdfAssetManager, PdfGenerator $pdfGenerator): Response
    {
        $filter = $request->query->get('filter', '');
        $amountPerPage = (int) $request->query->get('amount', '10');
        $pageNumber = (int) $request->query->get('page', '1');

        $data = $organismRepository->findByEnterpriseClient($filter, null, null);
        $paginator = new Paginator($data, null, null);
        if ($paginator->isFromGreaterThanTotal()) {
            return $paginator->greatherThanTotal($request, $router, $pageNumber);
        }

        return $this->renderPdf(
            $filter,
            $paginator,
            $pdfAssetManager,
            $pdfGenerator,
            'organism/pdf/amount_enterprise_client.twig',
            'Cantidad de clientes empresariales',
            'organismos_clientes_empresariales'
        );
    }

    #[Route('/amount_project_building_report', name: 'app_organism_amount_project_building_report', methods: ['GET'])]
    public function amountProjectAndBuildingReport(Request $request, RouterInterface $router, OrganismRepository $organismRepository): Response
    {
        $filter = $request->query->get('filter', '');
        $amountPerPage = (int) $request->query->get('amount', '10');
        $pageNumber = (int) $request->query->get('page', '1');

        $data = $organismRepository->findAmountProjectAndBuildings($filter, $amountPerPage, $pageNumber);
        $paginator = new Paginator($data, $amountPerPage, $pageNumber);
        if ($paginator->isFromGreaterThanTotal()) {
            return $paginator->greatherThanTotal($request, $router, $pageNumber);
        }

        $template = ($request->isXmlHttpRequest()) ? '_amount_project_building.html.twig' : 'report.html.twig';

        return $this->render("organism/report/$template", [
            'filter' => $filter,
            'paginator' => $paginator,
            'title' => 'Cantidad de proyectos y obras',
            'list' => '_amount_project_building',
        ]);
    }

    #[Route('/amount_project_building_report_print', name: 'app_organism_amount_project_building_report_print', methods: ['GET'])]
    public function amountProjectAndBuildingReportPrint(Request $request, OrganismRepository $organismRepository, RouterInterface $router, PdfAssetManager $pdfAssetManager, PdfGenerator $pdfGenerator): Response
    {
        $filter = $request->query->get('filter', '');
        $amountPerPage = (int) $request->query->get('amount', '10');
        $pageNumber = (int) $request->query->get('page', '1');

        $data = $organismRepository->findAmountProjectAndBuildings($filter, null, null);
        $paginator = new Paginator($data, null, null);
        if ($paginator->isFromGreaterThanTotal()) {
            return $paginator->greatherThanTotal($request, $router, $pageNumber);
        }

        return $this->renderPdf(
            $filter,
            $paginator,
            $pdfAssetManager,
            $pdfGenerator,
            'organism/pdf/amount_project_building.twig',
            'Cantidad de proyectos y obras',
            'organismos_proyectos_obras'
        );
    }

    /**
     * @throws Exception
     */
    #[Route('/amount_finance_report', name: 'app_organism_amount_finance_report', methods: ['GET'])]
    public function amountFinanceReport(Request $request, RouterInterface $router, OrganismRepository $organismRepository, InvestmentRepository $investmentRepository): Response
    {
        $response = $this->amountFinance($request, $router, $organismRepository, $investmentRepository);
        if ($response instanceof RedirectResponse) {
            return $response;
        }
        [$filter, $paginator] = $response;

        $template = ($request->isXmlHttpRequest()) ? '_amount_finance.html.twig' : 'report.html.twig';

        return $this->render("organism/report/$template", [
            'filter' => $filter,
            'paginator' => $paginator,
            'title' => 'Finanzas de obras por organismos',
            'list' => '_amount_finance',
            'currency' => 'CUP', // TODO: poner la moneda del sistema
        ]);
    }

    /**
     * @throws Exception
     */
    #[Route('/amount_finance_report_print', name: 'app_organism_amount_finance_report_print', methods: ['GET'])]
    public function amountFinanceReportPrint(Request $request, OrganismRepository $organismRepository, RouterInterface $router, PdfAssetManager $pdfAssetManager, PdfGenerator $pdfGenerator, InvestmentRepository $investmentRepository): Response
    {
        $response = $this->amountFinance($request, $router, $organismRepository, $investmentRepository, true);
        if ($response instanceof RedirectResponse) {
            return $response;
        }
        [$filter, $paginator] = $response;
        assert($paginator instanceof Paginator);

        return $this->renderPdf($filter, $paginator, $pdfAssetManager, $pdfGenerator, 'organism/pdf/amount_finance.twig', 'Finanzas de obras por organismo', 'organismos_finanzas', ['currency' => 'CUP']);
    }

    /**
     * @return RedirectResponse|array<mixed>
     *
     * @throws Exception
     */
    private function amountFinance(
        Request $request,
        RouterInterface $router,
        OrganismRepository $organismRepository,
        InvestmentRepository $investmentRepository,
        bool $pdf = false,
    ): RedirectResponse|array {
        $filter = $request->query->get('filter', '');
        $amountPerPage = (int) $request->query->get('amount', '10');
        $pageNumber = (int) $request->query->get('page', '1');

        if (true === $pdf) {
            $amountPerPage = null;
            $pageNumber = null;
        }

        $data = $organismRepository->findOrganisms($filter, $amountPerPage, $pageNumber);
        $newData = $this->addFinance($investmentRepository, $data);

        $paginator = new Paginator($newData, $amountPerPage, $pageNumber, count($organismRepository->findOrganisms($filter, null, null)));
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
    public function addFinance(InvestmentRepository $investmentRepository, \Doctrine\ORM\Tools\Pagination\Paginator $data): array
    {
        $newData = [];
        /* @var Organism $municipality */
        foreach ($data as $organism) {
            assert($organism instanceof Organism);

            $item = [];
            $item['id'] = $organism->getId();
            $item['name'] = $organism->getName();
            //            $item['province'] = $organism->getProvince()?->getName();

            $approvedValue = 0;
            $estimatedValue = 0;
            $estimatedAdjustValue = 0;
            $constructionAssembly = 0;
            $constructionRealValue = 0;

            /** @var Investment $investment */
            $investments = $investmentRepository->findBy(['municipality' => $organism->getId()]);
            foreach ($investments as $investment) {
                $projects = $investment->getProjects();
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
