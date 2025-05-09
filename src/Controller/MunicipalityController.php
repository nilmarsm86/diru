<?php

namespace App\Controller;

use App\Entity\Municipality;
use App\Repository\MunicipalityRepository;
use App\Service\CrudActionService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

#[Route('/municipality')]
#[IsGranted('ROLE_ADMIN')]
final class MunicipalityController extends AbstractController
{
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
            'ajax' => $request->isXmlHttpRequest()
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
            'ajax' => $request->isXmlHttpRequest()
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
        return $crudActionService->deleteAction($request, $municipalityRepository, $municipality, $successMsg, 'app_municipality_index');
    }

    #[Route('/options/{id}', name: 'app_municipality_options', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function options(Request $request, Municipality $municipality, MunicipalityRepository $municipalityRepository): Response
    {
        if ($request->isXmlHttpRequest()) {
            return $this->render('partials/_select_options.html.twig', [
                'entities' => $municipalityRepository->findBy([], ['name' => 'ASC']),
                'selected' => $municipality->getId()
            ]);
        }

        throw new BadRequestHttpException('Ajax request');
    }
}
