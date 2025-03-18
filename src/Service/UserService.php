<?php

namespace App\Service;

use App\DTO\ProfilePasswordForm;
use App\Form\ProfileFullNameType;
use App\Form\ProfilePasswordType;
use App\Repository\RoleRepository;
use App\Repository\UserRepository;
use Exception;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * User Service
 */
class UserService
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly RoleRepository $roleRepository,
        private readonly FormFactoryInterface $formFactory,
        private readonly Security $security,
        private readonly UserPasswordHasherInterface $userPasswordHasher,
        private readonly RequestStack $requestStack
    )
    {
    }

    /**
     * Add role to user
     * @param Request $request
     * @return array
     */
    public function addRole(Request $request): array
    {
        $user = $this->userRepository->find($request->request->get('user'));
        $role = $this->roleRepository->find($request->request->get('role'));

        $user->addRole($role);

        $this->userRepository->save($user, true);
        return [$user, $role];
        //mostrar mensaje de role agregado
    }

    /**
     * Remove role from user
     *
     * @param Request $request
     * @param bool $authorize
     * @return array
     * @throws Exception
     */
    public function removeRole(Request $request, bool $authorize): array
    {
        $user = $this->userRepository->find($request->request->get('user'));
        $role = $this->roleRepository->find($request->request->get('role'));

        try {
            $user->removeRole($role, !$authorize);
            $this->userRepository->save($user, true);
            return [$user, $role, 'text-bg-success', '', new Response()];
        }catch (Exception $exception){
            return [$user, $role, 'text-bg-danger', $exception->getMessage(), new Response('', 422)];
        }
    }

    /**
     * Handle name and lastname form
     *
     * @param Request $request
     * @return Form
     */
    public function handleNameForm(Request $request): FormInterface
    {
        $formName = $this->formFactory->create(ProfileFullNameType::class, $this->security->getUser());
        $formName->handleRequest($request);

        if($formName->isSubmitted() && $formName->isValid()){
            $this->userRepository->save($this->security->getUser(), true);

            $this->requestStack->getSession()->getFlashBag()->add('success', 'Datos salvados.');
        }

        return $formName;
    }

    /**
     * handle password form
     *
     * @param Request $request
     * @return Form
     */
    public function handlePassword(Request $request): FormInterface
    {
        $formPassword = $this->formFactory->create(ProfilePasswordType::class);
        $formPassword->handleRequest($request);

        if($formPassword->isSubmitted() && $formPassword->isValid()){
            /** @var ProfilePasswordForm $dto */
            $dto = $formPassword->getData();
            $user = $dto->toEntity($this->security->getUser());
            $user->changePassword($this->userPasswordHasher);
            $this->userRepository->save($user, true);

            $this->requestStack->getSession()->getFlashBag()->add('success', 'Contraseña cambiada.');
        }

        return $formPassword;
    }

    /**
     * Change state user
     *
     * @param Request $request
     * @return array
     */
    public function changeState(Request $request): array
    {
        $user = $this->userRepository->find($request->request->get('user'));
        $action = $request->request->get('action');
        ($action === 'activate') ? $user->activate() : $user->deactivate();

        $this->userRepository->save($user, true);

        return [$user, $action];
        //mostrar mensaje de cambio de estado de acivacion
    }

}