<?php

namespace App\Controller;

use App\DTO\Paginator;
use App\Entity\Enums\ProjectState;
use App\Entity\Enums\ProjectType;
use App\Entity\Enums\SubsystemFunctionalClassification;
use App\Entity\Province;
use App\Entity\Role;
use App\Entity\SubsystemType;
use App\Entity\SubsystemTypeSubsystemSubType;
use App\Form\SubsystemTypeType;
use App\Repository\SubsystemTypeRepository;
use App\Service\CrudActionService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

#[Route('/subsystem/type')]
#[IsGranted(Role::ROLE_ADMIN)]
final class SubsystemTypeController extends AbstractController
{
    #[Route(name: 'app_subsystem_type_index', methods: ['GET'])]
    public function index(Request $request, RouterInterface $router, SubsystemTypeRepository $subsystemTypeRepository, CrudActionService $crudActionService): Response
    {
        $filter = $request->query->get('filter', '');
        $amountPerPage = (int)$request->query->get('amount', '10');
        $pageNumber = (int)$request->query->get('page', '1');

        $classification = $request->query->get('classification', '');

        $data = $subsystemTypeRepository->findSubsystemsType($filter, $amountPerPage, $pageNumber, $classification);

        $paginator = new Paginator($data, $amountPerPage, $pageNumber);
        if ($paginator->isFromGreaterThanTotal()) {
            return $paginator->greatherThanTotal($request, $router, $pageNumber);
        }

        $template = ($request->isXmlHttpRequest()) ? '_list.html.twig' : 'index.html.twig';

        return $this->render("subsystem_type/$template", [
            'filter' => $filter,
            'paginator' => $paginator,
            'classifications' => SubsystemFunctionalClassification::cases()
        ]);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/new', name: 'app_subsystem_type_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CrudActionService $crudActionService): Response
    {
        $subsystemType = new SubsystemType();
        return $crudActionService->formLiveComponentAction($request, $subsystemType, 'subsystem_type', [
            'title' => 'Nuevo tipo',
        ]);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/{id}', name: 'app_subsystem_type_show', methods: ['GET'])]
    public function show(Request $request, SubsystemType $subsystemType, CrudActionService $crudActionService): Response
    {
        return $crudActionService->showAction($request, $subsystemType, 'subsystem_type', 'subsystem_type', 'Detalles del tipo');
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    #[Route('/{id}/edit', name: 'app_subsystem_type_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, SubsystemType $subsystemType, CrudActionService $crudActionService): Response
    {
        return $crudActionService->formLiveComponentAction($request, $subsystemType, 'subsystem_type', [
            'title' => 'Editar tipo',
        ]);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/{id}', name: 'app_subsystem_type_delete', methods: ['POST'])]
    public function delete(Request $request, SubsystemType $subsystemType, SubsystemTypeRepository $subsystemTypeRepository, CrudActionService $crudActionService): Response
    {
        $successMsg = 'Se ha eliminado el tipo.';
        return $crudActionService->deleteAction($request, $subsystemTypeRepository, $subsystemType, $successMsg, 'app_subsystem_type_index');
    }

    #[Route('/sub_type/{id}', name: 'type_subtype', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function subType(Request $request, SubsystemType $subsystemType): Response
    {
        if ($request->isXmlHttpRequest()) {
            $entities = [];
            /** @var SubsystemTypeSubsystemSubType $subsystemTypeSubsystemSubType */
            foreach ($subsystemType->getSubsystemTypeSubsystemSubTypes() as $subsystemTypeSubsystemSubType){
                if(!is_null($subsystemTypeSubsystemSubType->getSubsystemSubType())){
                    $entities[] = $subsystemTypeSubsystemSubType->getSubsystemSubType();
                }

            }

            return $this->render('partials/_select_options.html.twig', [
                'entities' => $entities,
                'selected' => (count($entities)) ? $entities[0]->getId() : 0,
                'empty' => '-Seleccione un tipo-'
            ]);
        }

        throw new BadRequestHttpException('Ajax request');
    }
}
