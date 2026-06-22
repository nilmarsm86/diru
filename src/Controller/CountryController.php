<?php

namespace App\Controller;

use App\Entity\Country;
use App\Entity\Role;
use App\Repository\CountryRepository;
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

#[Route('/country')]
#[IsGranted(Role::ROLE_ADMIN)]
final class CountryController extends AbstractController
{
    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route(name: 'app_country_index', methods: ['GET'])]
    public function index(Request $request, CountryRepository $countryRepository, CrudActionService $crudActionService): Response
    {
        return $crudActionService->indexAction($request, $countryRepository, 'findCountries', 'country');
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/new', name: 'app_country_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CrudActionService $crudActionService): Response
    {
        $country = new Country();

        return $crudActionService->formLiveComponentAction($request, $country, 'country', [
            'title' => 'Nuevo país',
        ]);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/{id}', name: 'app_country_show', methods: ['GET'])]
    public function show(Request $request, Country $country, CrudActionService $crudActionService): Response
    {
        return $crudActionService->showAction($request, $country, 'country', 'country', 'Detalles del país');
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/{id}/edit', name: 'app_country_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Country $country, CrudActionService $crudActionService): Response
    {
        return $crudActionService->formLiveComponentAction($request, $country, 'country', [
            'title' => 'Editar país',
        ]);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/{id}', name: 'app_country_delete', methods: ['POST'])]
    public function delete(Request $request, Country $country, CountryRepository $countryRepository, CrudActionService $crudActionService): Response
    {
        $successMsg = 'Se ha eliminado el país.';
        $response = $crudActionService->deleteAction($request, $countryRepository, $country, $successMsg, 'app_country_index');
        if ($response instanceof RedirectResponse) {
            $this->addFlash('success', $successMsg);

            return $response;
        }

        return $response;
    }

    #[Route('/city/{id}', name: 'city_country', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function city(Request $request, Country $country): Response
    {
        if ($request->isXmlHttpRequest()) {
            return $this->render('partials/_select_options.html.twig', [
                'entities' => $country->getCities(),
                'selected' => ($country->getCities()->count() > 0) ? (true === $country->getCities()->first() ? $country->getCities()->first()->getId() : 0) : 0,
                'empty' => '-Seleccione un país-',
            ]);
        }

        throw new BadRequestHttpException('Ajax request');
    }
}
