<?php

namespace App\Controller;

use App\Controller\Traits\PdfResponseTrait;
use App\DTO\Paginator;
use App\Entity\Investment;
use App\Entity\Municipality;
use App\Entity\Role;
use App\Repository\InvestmentRepository;
use App\Repository\MunicipalityRepository;
use App\Service\CrudActionService;
use App\Service\Pdf\PdfAssetManager;
use App\Service\Pdf\PdfGenerator;
use App\Service\UbicationReportService;
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

#[Route('/municipality')]
#[IsGranted(Role::ROLE_ADMIN)]
final class MunicipalityController extends AbstractController
{
    use PdfResponseTrait;

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route(name: 'app_municipality_index', methods: ['GET'])]
    public function index(Request $request, MunicipalityRepository $municipalityRepository, CrudActionService $crudActionService): Response
    {
        return $crudActionService->indexAction($request, $municipalityRepository, 'findMunicipalities', 'municipality');
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/new', name: 'app_municipality_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CrudActionService $crudActionService): Response
    {
        $municipality = new Municipality();

        return $crudActionService->formLiveComponentAction($request, $municipality, 'municipality', [
            'title' => 'Nuevo municipio',
        ]);
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    #[Route('/{id}', name: 'app_municipality_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(Request $request, Municipality $municipality, CrudActionService $crudActionService): Response
    {
        return $crudActionService->showAction($request, $municipality, 'municipality', 'municipality', 'Detalles del municipio');
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/{id}/edit', name: 'app_municipality_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function edit(Request $request, Municipality $municipality, CrudActionService $crudActionService): Response
    {
        return $crudActionService->formLiveComponentAction($request, $municipality, 'municipality', [
            'title' => 'Editar municipio',
        ]);
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    #[Route('/{id}', name: 'app_municipality_delete', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function delete(Request $request, Municipality $municipality, MunicipalityRepository $municipalityRepository, CrudActionService $crudActionService): Response
    {
        $successMsg = 'Se ha eliminado el municipio.';
        $response = $crudActionService->deleteAction($request, $municipalityRepository, $municipality, $successMsg, 'app_municipality_index');
        if ($response instanceof RedirectResponse) {
            $this->addFlash('success', $successMsg);

            return $response;
        }

        return $response;
    }

    #[Route('/print', name: 'app_municipality_print', methods: ['GET'])]
    public function print(Request $request, MunicipalityRepository $municipalityRepository, PdfAssetManager $pdfAssetManager, PdfGenerator $pdfGenerator): Response
    {
        $filter = $request->query->get('filter', '');

        $data = $municipalityRepository->findMunicipalities($filter, null, null);

        $paginator = new Paginator($data);

        return $this->renderPdf($filter, $paginator, $pdfAssetManager, $pdfGenerator, 'municipality/pdf/print.html.twig', 'Listado de municipios', 'municipios');
    }

    #[Route('/amount_project_report', name: 'app_municipality_amount_project_report', methods: ['GET'])]
    public function amountProjectReport(Request $request, RouterInterface $router, MunicipalityRepository $municipalityRepository, UbicationReportService $ubicationReport): Response
    {
        $response = $ubicationReport->amountProjectsAndBuildings($request, $router, $municipalityRepository);
        if ($response instanceof RedirectResponse) {
            return $response;
        }
        [$filter, $paginator] = $response;

        $template = ($request->isXmlHttpRequest()) ? '_amount_project.html.twig' : 'report.html.twig';

        return $this->render("municipality/report/$template", [
            'filter' => $filter,
            'paginator' => $paginator,
            'title' => 'Cantidad de proyectos y obras por municipio',
            'list' => '_amount_project',
        ]);
    }

    #[Route('/amount_project_report_print', name: 'app_municipality_amount_project_report_print', methods: ['GET'])]
    public function amountProjectReportPrint(Request $request, MunicipalityRepository $municipalityRepository, UbicationReportService $ubicationReport, RouterInterface $router, PdfAssetManager $pdfAssetManager, PdfGenerator $pdfGenerator): Response
    {
        $response = $ubicationReport->amountProjectsAndBuildings($request, $router, $municipalityRepository, true);
        if ($response instanceof RedirectResponse) {
            return $response;
        }
        [$filter, $paginator] = $response;
        assert($paginator instanceof Paginator);

        return $this->renderPdf($filter, $paginator, $pdfAssetManager, $pdfGenerator, 'municipality/pdf/amount_project.twig', 'Cantidad de proyectos y obras por municipio', 'municipios_proyectos_obras');
    }

    /**
     * @throws Exception
     */
    #[Route('/amount_client_report', name: 'app_municipality_amount_client_report', methods: ['GET'])]
    public function amountClientReport(Request $request, RouterInterface $router, MunicipalityRepository $municipalityRepository, UbicationReportService $ubicationReport): Response
    {
        $response = $ubicationReport->amountClients($request, $router, $municipalityRepository);
        if ($response instanceof RedirectResponse) {
            return $response;
        }
        [$filter, $paginator] = $response;

        $template = ($request->isXmlHttpRequest()) ? '_amount_client.html.twig' : 'report.html.twig';

        return $this->render("municipality/report/$template", [
            'filter' => $filter,
            'paginator' => $paginator,
            'title' => 'Cantidad de clientes por municipio',
            'list' => '_amount_client',
        ]);
    }

    /**
     * @throws Exception
     */
    #[Route('/amount_client_report_print', name: 'app_municipality_amount_client_report_print', methods: ['GET'])]
    public function amountClientReportPrint(Request $request, MunicipalityRepository $municipalityRepository, UbicationReportService $ubicationReport, RouterInterface $router, PdfAssetManager $pdfAssetManager, PdfGenerator $pdfGenerator): Response
    {
        $response = $ubicationReport->amountClients($request, $router, $municipalityRepository, true);
        if ($response instanceof RedirectResponse) {
            return $response;
        }
        [$filter, $paginator] = $response;
        assert($paginator instanceof Paginator);

        return $this->renderPdf($filter, $paginator, $pdfAssetManager, $pdfGenerator, 'municipality/pdf/amount_client.twig', 'Cantidad de clientes por municipio', 'municipios_clientes');
    }

    /**
     * @throws Exception
     */
    #[Route('/amount_finance_report', name: 'app_municipality_amount_finance_report', methods: ['GET'])]
    public function amountFinanceReport(Request $request, RouterInterface $router, MunicipalityRepository $municipalityRepository, InvestmentRepository $investmentRepository): Response
    {
        $response = $this->amountFinance($request, $router, $municipalityRepository, $investmentRepository);
        if ($response instanceof RedirectResponse) {
            return $response;
        }
        [$filter, $paginator] = $response;

        $template = ($request->isXmlHttpRequest()) ? '_amount_finance.html.twig' : 'report.html.twig';

        return $this->render("municipality/report/$template", [
            'filter' => $filter,
            'paginator' => $paginator,
            'title' => 'Finanzas de obras por municipio',
            'list' => '_amount_finance',
            'currency' => 'CUP', // TODO: poner la moneda del sistema
        ]);
    }

    /**
     * @throws Exception
     */
    #[Route('/amount_finance_report_print', name: 'app_municipality_amount_finance_report_print', methods: ['GET'])]
    public function amountFinanceReportPrint(Request $request, MunicipalityRepository $municipalityRepository, RouterInterface $router, PdfAssetManager $pdfAssetManager, PdfGenerator $pdfGenerator, InvestmentRepository $investmentRepository): Response
    {
        $response = $this->amountFinance($request, $router, $municipalityRepository, $investmentRepository, true);
        if ($response instanceof RedirectResponse) {
            return $response;
        }
        [$filter, $paginator] = $response;
        assert($paginator instanceof Paginator);

        return $this->renderPdf($filter, $paginator, $pdfAssetManager, $pdfGenerator, 'municipality/pdf/amount_finance.twig', 'Finanzas de obras por municipio', 'municipios_finanzas', ['currency' => 'CUP']);
    }

    /**
     * @return RedirectResponse|array<mixed>
     *
     * @throws Exception
     */
    private function amountFinance(
        Request $request,
        RouterInterface $router,
        MunicipalityRepository $municipalityRepository,
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

        $data = $municipalityRepository->findMunicipalities($filter, $amountPerPage, $pageNumber);
        $newData = $this->addFinance($investmentRepository, $data);

        $paginator = new Paginator($newData, $amountPerPage, $pageNumber, count($municipalityRepository->findMunicipalities($filter, null, null)));
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
        /* @var Municipality $municipality */
        foreach ($data as $municipality) {
            assert($municipality instanceof Municipality);

            $item = [];
            $item['id'] = $municipality->getId();
            $item['name'] = $municipality->getName();
            $item['province'] = $municipality->getProvince()?->getName();

            $approvedValue = 0;
            $estimatedValue = 0;
            $estimatedAdjustValue = 0;
            $constructionAssembly = 0;
            $constructionRealValue = 0;
            /** @var Investment $investment */
            $investments = $investmentRepository->findBy(['municipality' => $municipality->getId()]);
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
