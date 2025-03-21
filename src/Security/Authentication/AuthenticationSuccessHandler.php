<?php
namespace App\Security\Authentication;

use App\Repository\UserRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;

readonly class AuthenticationSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    public function __construct(private UrlGeneratorInterface $urlGenerator,private UserRepository $userRepository, private Security $security)
    {
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token): Response
    {
        $user = $this->userRepository->findOneBy(['username' => $request->request->get('_username', '')]);

        if(!$user->isActive()){
            $this->security->logout();
            return new RedirectResponse($this->urlGenerator->generate('app_login', ['inactive' => true]));
        }

        $request->getSession()->set('inactive_user', 0);
        return new RedirectResponse($this->urlGenerator->generate('app_home'));
    }
}