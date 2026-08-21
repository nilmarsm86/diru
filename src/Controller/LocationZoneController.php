<?php

namespace App\Controller;

use App\Controller\Traits\PdfResponseTrait;
use App\DTO\Paginator;
use App\Entity\LocationZone;
use App\Entity\Role;
use App\Repository\LocationZoneRepository;
use App\Service\CrudActionService;
use App\Service\Pdf\PdfAssetManager;
use App\Service\Pdf\PdfGenerator;
use App\Service\UbicationReportService;
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
#[Route('/location/zone')]
final class LocationZoneController extends AbstractController
{
    use PdfResponseTrait;

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route(name: 'app_location_zone_index', methods: ['GET'])]
    public function index(Request $request, LocationZoneRepository $locationZoneRepository, CrudActionService $crudActionService): Response
    {
        return $crudActionService->indexAction($request, $locationZoneRepository, 'findLocationZones', 'location_zone');
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/new', name: 'app_location_zone_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CrudActionService $crudActionService): Response
    {
        $locationZone = new LocationZone();

        return $crudActionService->formLiveComponentAction($request, $locationZone, 'location_zone', [
            'title' => 'Nueva zona de ubicación',
        ]);
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    #[Route('/{id}', name: 'app_location_zone_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(Request $request, LocationZone $locationZone, CrudActionService $crudActionService): Response
    {
        return $crudActionService->showAction($request, $locationZone, 'location_zone', 'location_zone', 'Detalles de la zona de ubicación');
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    #[Route('/{id}/edit', name: 'app_location_zone_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, LocationZone $locationZone, CrudActionService $crudActionService): Response
    {
        return $crudActionService->formLiveComponentAction($request, $locationZone, 'location_zone', [
            'title' => 'Editar zona de ubicación',
        ]);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[IsGranted(Role::ROLE_ADMIN)]
    #[Route('/{id}', name: 'app_location_zone_delete', methods: ['POST'])]
    public function delete(Request $request, LocationZone $locationZone, LocationZoneRepository $locationZoneRepository, CrudActionService $crudActionService): Response
    {
        $successMsg = 'Se ha eliminado la zona de ubicación.';
        $response = $crudActionService->deleteAction($request, $locationZoneRepository, $locationZone, $successMsg, 'app_location_zone_index');
        if ($response instanceof RedirectResponse) {
            $this->addFlash('success', $successMsg);

            return $response;
        }

        return $response;
    }

    #[Route('/print', name: 'app_location_zone_print', methods: ['GET'])]
    public function print(Request $request, LocationZoneRepository $locationZoneRepository, PdfAssetManager $pdfAssetManager, PdfGenerator $pdfGenerator): Response
    {
        $filter = $request->query->get('filter', '');

        $data = $locationZoneRepository->findLocationZones($filter, null, null);

        $paginator = new Paginator($data);

        return $this->renderPdf($filter, $paginator, $pdfAssetManager, $pdfGenerator, 'location_zone/pdf/print.html.twig', 'Listado de zonas de localización', 'zonas_localizacion');
    }

    #[Route('/amount_project_report', name: 'app_location_zone_amount_project_report', methods: ['GET'])]
    public function amountProjectReport(Request $request, RouterInterface $router, LocationZoneRepository $locationZoneRepository, UbicationReportService $ubicationReport): Response
    {
        $filter = $request->query->get('filter', '');
        $amountPerPage = (int) $request->query->get('amount', '10');
        $pageNumber = (int) $request->query->get('page', '1');

        $data = $locationZoneRepository->findAmountProject($filter, $amountPerPage, $pageNumber);
        $paginator = new Paginator($data, $amountPerPage, $pageNumber);
        if ($paginator->isFromGreaterThanTotal()) {
            return $paginator->greatherThanTotal($request, $router, $pageNumber);
        }

        $template = ($request->isXmlHttpRequest()) ? '_amount_project.html.twig' : 'report.html.twig';

        return $this->render("location_zone/report/$template", [
            'filter' => $filter,
            'paginator' => $paginator,
            'title' => 'Cantidad de inversiones y proyectos por zona de ubicación',
            'list' => '_amount_project',
        ]);
    }

    #[Route('/amount_project_report_print', name: 'app_location_zone_amount_project_report_print', methods: ['GET'])]
    public function amountProjectReportPrint(Request $request, LocationZoneRepository $locationZoneRepository, RouterInterface $router, PdfAssetManager $pdfAssetManager, PdfGenerator $pdfGenerator): Response
    {
        $filter = $request->query->get('filter', '');
        $amountPerPage = (int) $request->query->get('amount', '10');
        $pageNumber = (int) $request->query->get('page', '1');

        $data = $locationZoneRepository->findAmountProject($filter, null, null);
        $paginator = new Paginator($data, null, null);
        if ($paginator->isFromGreaterThanTotal()) {
            return $paginator->greatherThanTotal($request, $router, $pageNumber);
        }

        return $this->renderPdf(
            $filter,
            $paginator,
            $pdfAssetManager,
            $pdfGenerator,
            'location_zone/pdf/amount_project.twig',
            'Cantidad de inversiones y proyectos por zona de ubicación',
            'municipios_proyectos_obras'
        );
    }
}
