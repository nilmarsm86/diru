<?php
namespace App\Security\Authentication;

use App\Repository\UserRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;

readonly class AuthenticationSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    /**
     * @param UrlGeneratorInterface $urlGenerator
     * @param UserRepository $userRepository
     * @param Security $security
     */
    public function __construct(private UrlGeneratorInterface $urlGenerator,private UserRepository $userRepository, private Security $security)
    {
    }

    /**
     * @param Request $request
     * @param TokenInterface $token
     * @return Response
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token): Response
    {
        $user = $this->userRepository->findOneBy(['username' => $request->request->get('_username', '')]);

        if(!$user->isActive()){
            $this->security->logout();
            return new RedirectResponse($this->urlGenerator->generate('app_login', ['inactive' => true]));
        }

        return new RedirectResponse($this->urlGenerator->generate('app_home'));
    }
}