<?php

namespace App\Controller;

use App\Entity\Province;
use App\Entity\Role;
use App\Repository\ProvinceRepository;
use App\Service\CrudActionService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

#[Route('/province')]
#[IsGranted(Role::ROLE_ADMIN)]
final class ProvinceController extends AbstractController
{
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
}
