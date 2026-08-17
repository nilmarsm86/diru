<?php

namespace App\Controller;

use App\Controller\Traits\PdfResponseTrait;
use App\DTO\Paginator;
use App\Entity\Municipality;
use App\Entity\Role;
use App\Repository\MunicipalityRepository;
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
    public function print(Request $request, MunicipalityRepository $municipalityRepository, RouterInterface $router, PdfAssetManager $pdfAssetManager, PdfGenerator $pdfGenerator): Response
    {
        $filter = $request->query->get('filter', '');

        $data = $municipalityRepository->findMunicipalities($filter, null, null);

        $paginator = new Paginator($data);

        return $this->renderPdf($filter, $paginator, $pdfAssetManager, $pdfGenerator, 'municipality/pdf/print.html.twig', 'Listado de municipios', 'municipios');
    }

    #[Route('/amount_project_report', name: 'app_municipality_amount_project_report', methods: ['GET'])]
    public function amountProjectReport(Request $request, RouterInterface $router, MunicipalityRepository $municipalityRepository): Response
    {
        $response = $this->amountProjectsAndBuildings($request, $router, $municipalityRepository);
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
    public function amountProjectReportPrint(Request $request, MunicipalityRepository $municipalityRepository, RouterInterface $router, PdfAssetManager $pdfAssetManager, PdfGenerator $pdfGenerator): Response
    {
        $response = $this->amountProjectsAndBuildings($request, $router, $municipalityRepository, true);
        if ($response instanceof RedirectResponse) {
            return $response;
        }
        [$filter, $paginator] = $response;
        assert($paginator instanceof Paginator);

        return $this->renderPdf($filter, $paginator, $pdfAssetManager, $pdfGenerator, 'municipality/pdf/amount_project.twig', 'Cantidad de proyectos y obras por municipio', 'municipios_proyectos_obras');
    }

    /**
     * @return RedirectResponse|array<mixed>
     *
     * @throws Exception
     */
    private function amountProjectsAndBuildings(Request $request, RouterInterface $router, MunicipalityRepository $municipalityRepository, bool $pdf = false): RedirectResponse|array
    {
        $filter = $request->query->get('filter', '');
        $amountPerPage = (int) $request->query->get('amount', '10');
        $pageNumber = (int) $request->query->get('page', '1');

        if (true === $pdf) {
            $amountPerPage = null;
            $pageNumber = null;
        }

        $data = $municipalityRepository->findAmountProject($filter, $amountPerPage, $pageNumber);
        $countData = count($municipalityRepository->findAmountProject($filter, null, null));

        $paginator = new Paginator($data, $amountPerPage, $pageNumber, $countData);
        if ($paginator->isFromGreaterThanTotal()) {
            return $paginator->greatherThanTotal($request, $router, $pageNumber);
        }

        return [$filter, $paginator];
    }

    #[Route('/amount_client_report', name: 'app_municipality_amount_client_report', methods: ['GET'])]
    public function amountClientReport(Request $request, RouterInterface $router, MunicipalityRepository $municipalityRepository): Response
    {
        $response = $this->amountClients($request, $router, $municipalityRepository);
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

    #[Route('/amount_client_report_print', name: 'app_municipality_amount_client_report_print', methods: ['GET'])]
    public function amountClientReportPrint(Request $request, MunicipalityRepository $municipalityRepository, RouterInterface $router, PdfAssetManager $pdfAssetManager, PdfGenerator $pdfGenerator): Response
    {
        $response = $this->amountClients($request, $router, $municipalityRepository, true);
        if ($response instanceof RedirectResponse) {
            return $response;
        }
        [$filter, $paginator] = $response;
        assert($paginator instanceof Paginator);

        return $this->renderPdf($filter, $paginator, $pdfAssetManager, $pdfGenerator, 'municipality/pdf/amount_client.twig', 'Cantidad de clientes por municipio', 'municipios_clientes');
    }

    /**
     * @return RedirectResponse|array<mixed>
     *
     * @throws Exception
     */
    private function amountClients(Request $request, RouterInterface $router, MunicipalityRepository $municipalityRepository, bool $pdf = false): RedirectResponse|array
    {
        $filter = $request->query->get('filter', '');
        $amountPerPage = (int) $request->query->get('amount', '10');
        $pageNumber = (int) $request->query->get('page', '1');

        if (true === $pdf) {
            $amountPerPage = null;
            $pageNumber = null;
        }

        $data = $municipalityRepository->findAmountClients($filter, $amountPerPage, $pageNumber);
        $countData = count($municipalityRepository->findAmountClients($filter, null, null));

        $paginator = new Paginator($data, $amountPerPage, $pageNumber, $countData);
        if ($paginator->isFromGreaterThanTotal()) {
            return $paginator->greatherThanTotal($request, $router, $pageNumber);
        }

        return [$filter, $paginator];
    }
}
