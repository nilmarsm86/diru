<?php

namespace App\Controller;

use App\Entity\Person;
use App\Entity\Role;
use App\Repository\PersonRepository;
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

#[Route('/person')]
final class PersonController extends AbstractController
{
    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    #[IsGranted(Role::ROLE_DRAFTSMAN)]
    #[Route(name: 'app_person_index', methods: ['GET'])]
    public function index(Request $request, PersonRepository $personRepository, CrudActionService $crudActionService): Response
    {
        return $crudActionService->indexAction($request, $personRepository, 'findPersons', 'person');
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/new', name: 'app_person_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CrudActionService $crudActionService): Response
    {
        $person = new Person();
        return $crudActionService->formLiveComponentAction($request, $person, 'person', [
            'title' => 'Nuevo representante',
            'ajax' => $request->isXmlHttpRequest()
        ]);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[IsGranted('ROLE_DRAFTSMAN')]
    #[Route('/{id}', name: 'app_person_show', methods: ['GET'])]
    public function show(Request $request, Person $person, CrudActionService $crudActionService): Response
    {
        return $crudActionService->showAction($request, $person, 'person', 'person', 'Detalles del representante');
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/{id}/edit', name: 'app_person_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Person $person, CrudActionService $crudActionService): Response
    {
        return $crudActionService->formLiveComponentAction($request, $person, 'person', [
            'title' => 'Modificar representante',
            'ajax' => $request->isXmlHttpRequest()
        ]);
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/{id}', name: 'app_person_delete', methods: ['POST'])]
    public function delete(Request $request, Person $person, PersonRepository $personRepository, CrudActionService $crudActionService): Response
    {
        $successMsg = 'Se ha eliminado el representante.';
        return $crudActionService->deleteAction($request, $personRepository, $person, $successMsg, 'app_person_index');
    }

//    #[Route('/options/{id}', name: 'app_person_options', requirements: ['id' => '\d+'], methods: ['GET'])]
//    public function options(Request $request, Person $person, PersonRepository $personRepository): Response
//    {
////        if ($request->isXmlHttpRequest()) {
////            return $this->render('partials/_select_options.html.twig', [
////                'entities' => $personRepository->findBy([], ['name' => 'ASC']),
////                'selected' => $person->getId()
////            ]);
////        }
//
//        throw new BadRequestHttpException('Ajax request');
//    }
}
