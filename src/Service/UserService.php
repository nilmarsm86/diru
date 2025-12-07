<?php

namespace App\Service;

use App\DTO\ProfilePasswordForm;
use App\Entity\User;
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
readonly class UserService
{
    public function __construct(
        private UserRepository              $userRepository,
        private RoleRepository              $roleRepository,
        private FormFactoryInterface        $formFactory,
        private Security                    $security,
        private UserPasswordHasherInterface $userPasswordHasher,
//        private RequestStack                $requestStack
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

        try {
            $user->addRole($role);
            $this->userRepository->save($user, true);
            return [$user, $role, 'text-bg-success', '', new Response()];
        }catch (Exception $exception){
            return [$user, $role, 'text-bg-danger', $exception->getMessage(), new Response('', Response::HTTP_UNPROCESSABLE_ENTITY)];
        }
    }

    /**
     * Remove role from user
     *
     * @param Request $request
     * @param bool $authorize
     * @return array
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
            $code = ($exception->getCode() === 1) ? Response::HTTP_UNAUTHORIZED : Response::HTTP_UNPROCESSABLE_ENTITY;
            return [$user, $role, 'text-bg-danger', $exception->getMessage(), new Response('', $code)];
        }
    }

    /**
     * Handle name and lastname form
     *
     * @param Request $request
     * @return FormInterface
     */
    public function handleNameForm(Request $request): FormInterface
    {
        $formName = $this->formFactory->create(ProfileFullNameType::class, $this->security->getUser());
        $formName->handleRequest($request);
        if($formName->isSubmitted() && $formName->isValid()){
            $this->userRepository->save($this->security->getUser(), true);

//            $this->requestStack->getSession()->getFlashBag()->add('success', 'Datos salvados.');
        }

        return $formName;
    }

    /**
     * handle password form
     *
     * @param Request $request
     * @return FormInterface
     */
    public function handlePassword(Request $request): FormInterface
    {
        $formPassword = $this->formFactory->create(ProfilePasswordType::class);
        $formPassword->handleRequest($request);

        if($formPassword->isSubmitted() && $formPassword->isValid()){
            /** @var ProfilePasswordForm $dto */
            $dto = $formPassword->getData();
            $user = $this->security->getUser();
            assert($user instanceof User);
            $user = $dto->toEntity($user);
            $user->changePassword($this->userPasswordHasher);
            $this->userRepository->save($user, true);

//            $this->requestStack->getSession()->getFlashBag()->add('success', 'Contraseña cambiada.');
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
    }

}
