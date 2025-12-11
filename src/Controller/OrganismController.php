<?php

namespace App\Controller;

use App\Entity\Organism;
use App\Entity\Role;
use App\Repository\CorporateEntityRepository;
use App\Repository\OrganismRepository;
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

#[IsGranted(Role::ROLE_DRAFTSMAN)]
#[Route('/organism')]
final class OrganismController extends AbstractController
{
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
            //            'ajax' => $request->isXmlHttpRequest()
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
            //            'ajax' => $request->isXmlHttpRequest()
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
        if (count($corporateEntities)) {
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

    //    #[Route('/options/{id}', name: 'app_organism_options', requirements: ['id' => '\d+'], methods: ['GET'])]
    //    public function options(Request $request, Organism $organism, OrganismRepository $organismRepository): Response
    //    {
    // //        if ($request->isXmlHttpRequest()) {
    // //            return $this->render('partials/_select_options.html.twig', [
    // //                'entities' => $organismRepository->findBy([], ['name' => 'ASC']),
    // //                'selected' => $organism->getId()
    // //            ]);
    // //        }
    //
    //        throw new BadRequestHttpException('Ajax request');
    //    }
}
