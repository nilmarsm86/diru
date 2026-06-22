<?php

namespace App\Controller;

use App\Entity\City;
use App\Entity\Role;
use App\Repository\CityRepository;
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

#[Route('/city')]
#[IsGranted(Role::ROLE_ADMIN)]
final class CityController extends AbstractController
{
    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route(name: 'app_city_index', methods: ['GET'])]
    public function index(Request $request, CityRepository $cityRepository, CrudActionService $crudActionService): Response
    {
        return $crudActionService->indexAction($request, $cityRepository, 'findcities', 'city');
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    #[Route('/new', name: 'app_city_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CrudActionService $crudActionService): Response
    {
        $city = new City();

        return $crudActionService->formLiveComponentAction($request, $city, 'city', [
            'title' => 'Nueva ciudad',
        ]);
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    #[Route('/{id}', name: 'app_city_show', methods: ['GET'])]
    public function show(Request $request, City $city, CrudActionService $crudActionService): Response
    {
        return $crudActionService->showAction($request, $city, 'city', 'city', 'Detalles de la ciudad');
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/{id}/edit', name: 'app_city_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, City $city, CrudActionService $crudActionService): Response
    {
        return $crudActionService->formLiveComponentAction($request, $city, 'city', [
            'title' => 'Editar ciudad',
        ]);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/{id}', name: 'app_city_delete', methods: ['POST'])]
    public function delete(Request $request, City $city, CityRepository $cityRepository, CrudActionService $crudActionService): Response
    {
        $successMsg = 'Se ha eliminado la ciudad.';
        $response = $crudActionService->deleteAction($request, $cityRepository, $city, $successMsg, 'app_city_index');
        if ($response instanceof RedirectResponse) {
            $this->addFlash('success', $successMsg);

            return $response;
        }

        return $response;
    }
}
