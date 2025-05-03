<?php

namespace App\Controller;

use App\DTO\RegistrationForm;
use App\Entity\Role;
use App\Form\RegistrationFormType;
use App\Repository\RoleRepository;
use App\Repository\UserRepository;
use Exception;
use LogicException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserRepository $userRepository, RoleRepository $rolRepository): Response
    {
        if(!is_null($this->getUser())){
            return $this->redirectToRoute('app_home');
        }

        $error = false;
        $message = '';

        $form = $this->createForm(RegistrationFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $roleUser = $rolRepository->findOneBy(['name' => Role::ROLE_CLIENT]);

            /** @var RegistrationForm $dto */
            $dto = $form->getData();
            $user = $dto->toEntity();
            $user->register($userPasswordHasher, $roleUser);

            try {
                $userRepository->save($user, true);
                // do anything else you need here, like send an email
                $this->addFlash('success', 'Se a registrado correctamente en el sistema. Espere que le activen el usuario para autenticarse.');

                return $this->redirectToRoute('app_login');
            }catch (Exception $exception){
                $error = true;
                $message = 'Ha ocurrido un error al registrar al usuario. Revise bien los datos.';
            }
        }

        return $this->render('security/register.html.twig', [
            'form' => $form->createView(),
            'error' => $error,
            'message' => $message
        ]);
    }
}
