<?php

namespace App\Controller;

use App\Entity\Currency;
use App\Entity\Role;
use App\Repository\CurrencyRepository;
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

#[IsGranted(Role::ROLE_ADMIN)]
#[Route('/currency')]
final class CurrencyController extends AbstractController
{
    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route(name: 'app_currency_index', methods: ['GET'])]
    public function index(Request $request, CurrencyRepository $currencyRepository, CrudActionService $crudActionService): Response
    {
        return $crudActionService->indexAction($request, $currencyRepository, 'findCurrencies', 'currency');
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    #[Route('/new', name: 'app_currency_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CrudActionService $crudActionService): Response
    {
        $currency = new Currency();

        return $crudActionService->formLiveComponentAction($request, $currency, 'currency', [
            'title' => 'Nueva moneda',
        ]);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/{id}', name: 'app_currency_show', methods: ['GET'])]
    public function show(Request $request, Currency $currency, CrudActionService $crudActionService): Response
    {
        return $crudActionService->showAction($request, $currency, 'currency', 'currency', 'Detalles de la moneda');
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/{id}/edit', name: 'app_currency_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Currency $currency, CrudActionService $crudActionService): Response
    {
        return $crudActionService->formLiveComponentAction($request, $currency, 'currency', [
            'title' => 'Editar moneda',
        ]);
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    #[Route('/{id}', name: 'app_currency_delete', methods: ['POST'])]
    public function delete(Request $request, Currency $currency, CurrencyRepository $currencyRepository, CrudActionService $crudActionService): Response
    {
        $successMsg = 'Se ha eliminado la moneda.';
        $response = $crudActionService->deleteAction($request, $currencyRepository, $currency, $successMsg, 'app_currency_index');
        if ($response instanceof RedirectResponse) {
            $this->addFlash('success', $successMsg);

            return $response;
        }

        return $response;
    }
}
