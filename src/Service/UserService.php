<?php

namespace App\Service;

use App\DTO\ProfilePasswordForm;
use App\Entity\Role;
use App\Entity\User;
use App\Form\ProfileFullNameType;
use App\Form\ProfilePasswordType;
use App\Repository\RoleRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * User Service.
 */
readonly class UserService
{
    public function __construct(
        private UserRepository              $userRepository,
        private RoleRepository              $roleRepository,
        private FormFactoryInterface        $formFactory,
        private Security                    $security,
        private UserPasswordHasherInterface $userPasswordHasher,
    )
    {
    }

    /**
     * Add role to user.
     *
     * @return array<mixed>
     */
    public function addRole(Request $request): array
    {
        $user = $this->userRepository->find($request->request->get('user'));
        $role = $this->roleRepository->find($request->request->get('role'));

        try {
            if (!$user instanceof User) {
                throw new AccessDeniedException('Usuario no autenticado');
            }

            assert($role instanceof Role);
            $user->addRole($role);
            $this->userRepository->save($user, true);

            return [$user, $role, 'text-bg-success', '', new Response()];
        } catch (\Exception $exception) {
            return [$user, $role, 'text-bg-danger', $exception->getMessage(), new Response('', Response::HTTP_UNPROCESSABLE_ENTITY)];
        }
    }

    /**
     * Remove role from user.
     *
     * @return array<mixed>
     */
    public function removeRole(Request $request, bool $authorize): array
    {
        $user = $this->userRepository->find($request->request->get('user'));
        $role = $this->roleRepository->find($request->request->get('role'));

        try {
            if (!$user instanceof User) {
                throw new AccessDeniedException('Usuario no autenticado');
            }

            assert($role instanceof Role);
            $user->removeRole($role, !$authorize);
            $this->userRepository->save($user, true);

            return [$user, $role, 'text-bg-success', '', new Response()];
        } catch (\Exception $exception) {
            $code = (1 === $exception->getCode()) ? Response::HTTP_UNAUTHORIZED : Response::HTTP_UNPROCESSABLE_ENTITY;

            return [$user, $role, 'text-bg-danger', $exception->getMessage(), new Response('', $code)];
        }
    }

    /**
     * Handle name and lastname form.
     *
     * @return FormInterface<UserInterface|null>
     */
    public function handleNameForm(Request $request): FormInterface
    {
        $formName = $this->formFactory->create(ProfileFullNameType::class, $this->security->getUser());
        $formName->handleRequest($request);
        if ($formName->isSubmitted() && $formName->isValid()) {
            $user = $this->security->getUser();
            if (!$user instanceof User) {
                throw new AccessDeniedException('Usuario no autenticado');
            }
            $this->userRepository->save($user, true);
        }

        return $formName;
    }

    /**
     * handle password form.
     *
     * @return FormInterface<ProfilePasswordForm>
     */
    public function handlePassword(Request $request): FormInterface
    {
        $formPassword = $this->formFactory->create(ProfilePasswordType::class);
        $formPassword->handleRequest($request);

        if ($formPassword->isSubmitted() && $formPassword->isValid()) {
            /** @var ProfilePasswordForm $dto */
            $dto = $formPassword->getData();
            $user = $this->security->getUser();
            assert($user instanceof User);
            $user = $dto->toEntity($user);
            $user->changePassword($this->userPasswordHasher);
            $this->userRepository->save($user, true);
        }

        return $formPassword;
    }

    /**
     * Change state user.
     *
     * @return array<mixed>
     */
    public function changeState(Request $request): array
    {
        $user = $this->userRepository->find($request->request->get('user'));
        if (!$user instanceof User) {
            throw new AccessDeniedException('Usuario no autenticado');
        }

        $action = $request->request->get('action');
        ('activate' === $action) ? $user->activate() : $user->deactivate();

        $this->userRepository->save($user, true);

        return [$user, $action];
    }
}
