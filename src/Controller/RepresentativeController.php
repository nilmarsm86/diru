<?php

namespace App\Controller;

use App\Entity\Representative;
use App\Entity\Role;
use App\Repository\RepresentativeRepository;
use App\Service\CrudActionService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

#[Route('/representative')]
final class RepresentativeController extends AbstractController
{
    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    #[IsGranted(Role::ROLE_DRAFTSMAN)]
    #[Route(name: 'app_representative_index', methods: ['GET'])]
    public function index(Request $request, RepresentativeRepository $representativeRepository, CrudActionService $crudActionService): Response
    {
        return $crudActionService->indexAction($request, $representativeRepository, 'findRepresentatives', 'representative');
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/new', name: 'app_representative_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CrudActionService $crudActionService): Response
    {
        $representative = new Representative();

        return $crudActionService->formLiveComponentAction($request, $representative, 'representative', [
            'title' => 'Nuevo representante',
        ]);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[IsGranted('ROLE_DRAFTSMAN')]
    #[Route('/{id}', name: 'app_representative_show', methods: ['GET'])]
    public function show(Request $request, Representative $representative, CrudActionService $crudActionService): Response
    {
        return $crudActionService->showAction($request, $representative, 'representative', 'representative', 'Detalles del representante');
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/{id}/edit', name: 'app_representative_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Representative $representative, CrudActionService $crudActionService): Response
    {
        return $crudActionService->formLiveComponentAction($request, $representative, 'representative', [
            'title' => 'Modificar representante',
        ]);
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/{id}', name: 'app_representative_delete', methods: ['POST'])]
    public function delete(Request $request, Representative $representative, RepresentativeRepository $representativeRepository, CrudActionService $crudActionService): Response
    {
        $successMsg = 'Se ha eliminado el representante.';
        $response = $crudActionService->deleteAction($request, $representativeRepository, $representative, $successMsg, 'app_representative_index');
        if ($response instanceof RedirectResponse) {
            $this->addFlash('success', $successMsg);

            return $response;
        }

        return $response;
    }

    //    #[Route('/options/{id}', name: 'app_representative_options', requirements: ['id' => '\d+'], methods: ['GET'])]
    //    public function options(Request $request, Representative $representative, RepresentativeRepository $representativeRepository): Response
    //    {
    // //        if ($request->isXmlHttpRequest()) {
    // //            return $this->render('partials/_select_options.html.twig', [
    // //                'entities' => $representativeRepository->findBy([], ['name' => 'ASC']),
    // //                'selected' => $representative->getId()
    // //            ]);
    // //        }
    //
    //        throw new BadRequestHttpException('Ajax request');
    //    }
}
