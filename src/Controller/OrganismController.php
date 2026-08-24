<?php

namespace App\Controller;

use App\Controller\Traits\PdfResponseTrait;
use App\DTO\Paginator;
use App\Entity\Organism;
use App\Entity\Role;
use App\Repository\CorporateEntityRepository;
use App\Repository\OrganismRepository;
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
    public function amountCorporateEntityReport(Request $request, RouterInterface $router, OrganismRepository $organismRepository, UbicationReportService $ubicationReport): Response
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
}
