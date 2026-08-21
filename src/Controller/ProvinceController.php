<?php

namespace App\Controller;

use App\Controller\Traits\PdfResponseTrait;
use App\DTO\Paginator;
use App\Entity\Investment;
use App\Entity\Province;
use App\Entity\Role;
use App\Repository\InvestmentRepository;
use App\Repository\MunicipalityRepository;
use App\Repository\ProvinceRepository;
use App\Service\CrudActionService;
use App\Service\Pdf\PdfAssetManager;
use App\Service\Pdf\PdfGenerator;
use App\Service\UbicationReportService;
use Doctrine\DBAL\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

#[Route('/province')]
#[IsGranted(Role::ROLE_ADMIN)]
final class ProvinceController extends AbstractController
{
    use PdfResponseTrait;

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route(name: 'app_province_index', methods: ['GET'])]
    public function index(Request $request, ProvinceRepository $provinceRepository, CrudActionService $crudActionService): Response
    {
        return $crudActionService->indexAction($request, $provinceRepository, 'findProvinces', 'province');
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/new', name: 'app_province_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CrudActionService $crudActionService): Response
    {
        $province = new Province();

        return $crudActionService->formLiveComponentAction($request, $province, 'province', [
            'title' => 'Nueva provincia',
        ]);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/{id}', name: 'app_province_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(Request $request, Province $province, CrudActionService $crudActionService): Response
    {
        return $crudActionService->showAction($request, $province, 'province', 'province', 'Detalles de la provincia');
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/{id}/edit', name: 'app_province_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function edit(Request $request, Province $province, CrudActionService $crudActionService): Response
    {
        return $crudActionService->formLiveComponentAction($request, $province, 'province', [
            'title' => 'Editar provincia',
        ]);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/{id}', name: 'app_province_delete', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function delete(Request $request, Province $province, ProvinceRepository $provinceRepository, CrudActionService $crudActionService): Response
    {
        $successMsg = 'Se ha eliminado la provincia.';
        $response = $crudActionService->deleteAction($request, $provinceRepository, $province, $successMsg, 'app_province_index');
        if ($response instanceof RedirectResponse) {
            $this->addFlash('success', $successMsg);

            return $response;
        }

        return $response;
    }

    #[Route('/municipality/{id}', name: 'province_municipality', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function municipality(Request $request, Province $province): Response
    {
        if ($request->isXmlHttpRequest()) {
            return $this->render('partials/_select_options.html.twig', [
                'entities' => $province->getMunicipalities(),
                'selected' => ($province->getMunicipalities()->count() > 0) ? (true === $province->getMunicipalities()->first() ? $province->getMunicipalities()->first()->getId() : 0) : 0,
                'empty' => '-Seleccione una provincia-',
            ]);
        }

        throw new BadRequestHttpException('Ajax request');
    }

    #[Route('/print', name: 'app_province_print', methods: ['GET'])]
    public function print(Request $request, ProvinceRepository $provinceRepository, PdfAssetManager $pdfAssetManager, PdfGenerator $pdfGenerator): Response
    {
        $filter = $request->query->get('filter', '');

        $data = $provinceRepository->findProvinces($filter, null, null);

        $paginator = new Paginator($data);

        return $this->renderPdf($filter, $paginator, $pdfAssetManager, $pdfGenerator, 'province/pdf/print.html.twig', 'Listado de provincias', 'provincias');
    }

    #[Route('/amount_project_report', name: 'app_province_amount_project_report', methods: ['GET'])]
    public function amountProjectReport(Request $request, RouterInterface $router, ProvinceRepository $provinceRepository, UbicationReportService $ubicationReport): Response
    {
        $response = $ubicationReport->amountProjectsAndBuildings($request, $router, $provinceRepository);
        if ($response instanceof RedirectResponse) {
            return $response;
        }
        [$filter, $paginator] = $response;

        $template = ($request->isXmlHttpRequest()) ? '_amount_project.html.twig' : 'report.html.twig';

        return $this->render("province/report/$template", [
            'filter' => $filter,
            'paginator' => $paginator,
            'title' => 'Cantidad de proyectos y obras por provincia',
            'list' => '_amount_project',
        ]);
    }

    #[Route('/amount_project_report_print', name: 'app_province_amount_project_report_print', methods: ['GET'])]
    public function amountProjectReportPrint(Request $request, ProvinceRepository $provinceRepository, UbicationReportService $ubicationReport, RouterInterface $router, PdfAssetManager $pdfAssetManager, PdfGenerator $pdfGenerator): Response
    {
        $response = $ubicationReport->amountProjectsAndBuildings($request, $router, $provinceRepository, true);
        if ($response instanceof RedirectResponse) {
            return $response;
        }
        [$filter, $paginator] = $response;
        assert($paginator instanceof Paginator);

        return $this->renderPdf($filter, $paginator, $pdfAssetManager, $pdfGenerator, 'province/pdf/amount_project.twig', 'Cantidad de proyectos y obras por provincia', 'provincias_proyectos_obras');
    }

    /**
     * @throws Exception
     */
    #[Route('/amount_client_report', name: 'app_province_amount_client_report', methods: ['GET'])]
    public function amountClientReport(Request $request, RouterInterface $router, ProvinceRepository $provinceRepository, UbicationReportService $ubicationReport): Response
    {
        $response = $ubicationReport->amountClients($request, $router, $provinceRepository);
        if ($response instanceof RedirectResponse) {
            return $response;
        }
        [$filter, $paginator] = $response;

        $template = ($request->isXmlHttpRequest()) ? '_amount_client.html.twig' : 'report.html.twig';

        return $this->render("province/report/$template", [
            'filter' => $filter,
            'paginator' => $paginator,
            'title' => 'Cantidad de clientes por provincia',
            'list' => '_amount_client',
        ]);
    }

    /**
     * @throws Exception
     */
    #[Route('/amount_client_report_print', name: 'app_province_amount_client_report_print', methods: ['GET'])]
    public function amountClientReportPrint(Request $request, ProvinceRepository $provinceRepository, UbicationReportService $ubicationReport, RouterInterface $router, PdfAssetManager $pdfAssetManager, PdfGenerator $pdfGenerator): Response
    {
        $response = $ubicationReport->amountClients($request, $router, $provinceRepository, true);
        if ($response instanceof RedirectResponse) {
            return $response;
        }
        [$filter, $paginator] = $response;
        assert($paginator instanceof Paginator);

        return $this->renderPdf($filter, $paginator, $pdfAssetManager, $pdfGenerator, 'province/pdf/amount_client.twig', 'Cantidad de clientes por provincia', 'provincias_clientes');
    }

    /**
     * @throws Exception
     */
    #[Route('/amount_finance_report', name: 'app_province_amount_finance_report', methods: ['GET'])]
    public function amountFinanceReport(Request $request, RouterInterface $router, ProvinceRepository $provinceRepository, InvestmentRepository $investmentRepository): Response
    {
        $response = $this->amountFinance($request, $router, $provinceRepository, $investmentRepository);
        if ($response instanceof RedirectResponse) {
            return $response;
        }
        [$filter, $paginator] = $response;

        $template = ($request->isXmlHttpRequest()) ? '_amount_finance.html.twig' : 'report.html.twig';

        return $this->render("province/report/$template", [
            'filter' => $filter,
            'paginator' => $paginator,
            'title' => 'Finanzas de obras por provincia',
            'list' => '_amount_finance',
            'currency' => 'CUP', // TODO: poner la moneda del sistema
        ]);
    }

    /**
     * @throws Exception
     */
    #[Route('/amount_finance_report_print', name: 'app_province_amount_finance_report_print', methods: ['GET'])]
    public function amountFinanceReportPrint(Request $request, ProvinceRepository $provinceRepository, RouterInterface $router, PdfAssetManager $pdfAssetManager, PdfGenerator $pdfGenerator, InvestmentRepository $investmentRepository): Response
    {
        $response = $this->amountFinance($request, $router, $provinceRepository, $investmentRepository, true);
        if ($response instanceof RedirectResponse) {
            return $response;
        }
        [$filter, $paginator] = $response;
        assert($paginator instanceof Paginator);

        return $this->renderPdf($filter, $paginator, $pdfAssetManager, $pdfGenerator, 'province/pdf/amount_finance.twig', 'Finanzas de obras por provincia', 'provincias_finanzas', ['currency' => 'CUP']);
    }

    /**
     * @return RedirectResponse|array<mixed>
     *
     * @throws Exception
     */
    private function amountFinance(
        Request $request,
        RouterInterface $router,
        ProvinceRepository $provinceRepository,
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

        $data = $provinceRepository->findProvinces($filter, $amountPerPage, $pageNumber);
        $newData = $this->addFinance($investmentRepository, $data);

        $paginator = new Paginator($newData, $amountPerPage, $pageNumber, count($provinceRepository->findProvinces($filter, null, null)));
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
        /* @var Province $province */
        foreach ($data as $province) {
            assert($province instanceof Province);

            $item = [];
            $item['id'] = $province->getId();
            $item['name'] = $province->getName();

            $approvedValue = 0;
            $estimatedValue = 0;
            $estimatedAdjustValue = 0;
            $constructionAssembly = 0;
            $constructionRealValue = 0;
            $municipalities = $province->getMunicipalities();
            foreach ($municipalities as $municipality) {
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
