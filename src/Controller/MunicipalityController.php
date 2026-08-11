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

        $html = $this->renderView('municipality/pdf/print.html.twig', [
            'filter' => $filter,
            'paginator' => $paginator,
            'logo' => $pdfAssetManager->getLogoBase64(),
            'title' => 'Listado de municipios',
        ]);

        $pdfContent = $pdfGenerator->generate($html);

        return $this->pdfResponse($pdfContent, 'municipios');
    }
}
