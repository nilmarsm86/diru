<?php

namespace App\Controller;

use App\DTO\Paginator;

use App\Entity\Role;
use App\Repository\RoleRepository;
use App\Repository\UserRepository;
use App\Service\CrudActionService;
use App\Service\UserService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

#[Route('/user')]
class UserController extends AbstractController
{
    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    #[Route('/', name: 'user_list')]
    #[IsGranted(Role::ROLE_ADMIN)]
    public function index(Request $request, UserRepository $userRepository, RoleRepository $roleRepository, CrudActionService $crudActionService): Response
    {
        return $crudActionService->indexAction($request, $userRepository, 'findUsers', 'user', [
            'roles' => $roleRepository->findBy([], ['importance' => 'ASC']),
        ]);
    }

    #[Route('/add_role', name: 'add_role', methods: ['POST'])]
    #[IsGranted(Role::ROLE_ADMIN)]
    public function addRole(Request $request, UserService $userService): Response
    {
        if($request->isXmlHttpRequest() && ($request->query->get('fetch') === '1')){
            list($user, $role, $type, $message, $response) = $userService->addRole($request);

            return $this->render('user/_add_remove_role.html.twig', [
                'id' => 'add_'.$user->getId().'-'.$role->getId(),
                'role' => $role,
                'user' => $user,
                'action' => 'Establecido',
                'type' => $type,
                'message' => $message
            ], $response);
        }

        throw new BadRequestHttpException('Ajax request');
    }

    #[Route('/remove_role', name: 'remove_role', methods: ['POST'])]
    #[IsGranted(Role::ROLE_ADMIN)]
    public function removeRole(Request $request, UserService $userService): Response
    {
        if($request->isXmlHttpRequest() && ($request->query->get('fetch') === '1')){
            list($user, $role, $type, $message, $response) = $userService->removeRole($request, $this->isGranted('ROLE_SUPER_ADMIN'));

            return $this->render('user/_add_remove_role.html.twig', [
                    'id' => 'remove_'.$user->getId().'-'.$role->getId(),
                    'role' => $role,
                    'user' => $user,
                    'action' => 'Eliminado',
                    'type' => $type,
                    'message' => $message
                ], $response);
        }

        throw new BadRequestHttpException('Ajax request');
    }

    #[Route('/profile', name: 'user_profile')]
    #[IsGranted(Role::ROLE_CLIENT)]
    public function profile(Request $request, RoleRepository $roleRepository, UserService $userService): Response
    {
        $formName = $userService->handleNameForm($request);
        $formPassword = $userService->handlePassword($request);

        return $this->render('user/profile.html.twig', [
            'roles' => $roleRepository->findAll(),
            'formName' => $formName->createView(),
            'formPassword' => $formPassword->createView(),
        ]);
    }

    #[Route('/state', name: 'user_state', methods: ['POST'])]
    #[IsGranted(Role::ROLE_ADMIN)]
    public function state(Request $request, UserService $userService): Response
    {
        if($request->isXmlHttpRequest() && ($request->query->get('fetch') === '1')){
            list($user, $action) = $userService->changeState($request);

            return $this->render('user/_activate_deactivate_user.html.twig', [
                'id' => $action.'_'.$user->getId(),
                'user' => $user,
                'action' => ($action === 'activate') ? 'Activado' : 'Inactivo',
                'type' => 'text-bg-success',
                'message' => null
            ]);
        }

        throw new BadRequestHttpException('Ajax request');
    }

}
