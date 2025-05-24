<?php

namespace App\Controller;

use App\Entity\Constructor;
use App\Entity\Role;
use App\Form\ConstructorType;
use App\Repository\ConstructorRepository;
use App\Service\CrudActionService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

#[IsGranted(Role::ROLE_DRAFTSMAN)]
#[Route('/constructor')]
final class ConstructorController extends AbstractController
{
    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    #[Route(name: 'app_constructor_index', methods: ['GET'])]
    public function index(Request $request, ConstructorRepository $constructorRepository, CrudActionService $crudActionService): Response
    {
        return $crudActionService->indexAction($request, $constructorRepository, 'findConstructors', 'constructor');
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    #[Route('/new', name: 'app_constructor_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CrudActionService $crudActionService): Response
    {
        $constructor = new Constructor();
        return $crudActionService->formLiveComponentAction($request, $constructor, 'constructor', [
            'title' => 'Nueva constructora',
//            'ajax' => $request->isXmlHttpRequest()
        ]);
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    #[Route('/{id}', name: 'app_constructor_show', methods: ['GET'])]
    public function show(Request $request, Constructor $constructor, CrudActionService $crudActionService): Response
    {
        return $crudActionService->showAction($request, $constructor, 'constructor', 'constructor', 'Detalles de la constructora');
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */

    #[Route('/{id}/edit', name: 'app_constructor_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Constructor $constructor, CrudActionService $crudActionService): Response
    {
        return $crudActionService->formLiveComponentAction($request, $constructor, 'constructor', [
            'title' => 'Editar constructora',
//            'ajax' => $request->isXmlHttpRequest()
        ]);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[IsGranted(Role::ROLE_ADMIN)]
    #[Route('/{id}', name: 'app_constructor_delete', methods: ['POST'])]
    public function delete(Request $request, Constructor $constructor, ConstructorRepository $constructorRepository, CrudActionService $crudActionService): Response
    {
        $successMsg = 'Se ha eliminado la constructora.';
        return $crudActionService->deleteAction($request, $constructorRepository, $constructor, $successMsg, 'app_constructor_index');
    }
}
