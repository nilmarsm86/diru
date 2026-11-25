<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;
use Symfony\Component\Security\Http\Event\LogoutEvent;

final class SingleSessionSubscriber implements EventSubscriberInterface
{
    private const KEY = 'single_session_token';

    public function __construct(
        private Security $security,
        private EntityManagerInterface $em,
        private UrlGeneratorInterface $urls,
    ) {
    }

    //    #[AsEventListener(event: LoginSuccessEvent::class)]
    public function onLogin(LoginSuccessEvent $event): void
    {
        $user = $event->getUser();
        if (!$user instanceof User) {
            return;
        }

        $token = bin2hex(random_bytes(32));
        $user->setSessionToken($token);
        $this->em->flush();

        $event->getRequest()->getSession()->set(self::KEY, $token);
    }

    //    #[AsEventListener(event: LogoutEvent::class)]
    public function onLogout(LogoutEvent $event): void
    {
        $user = $event->getToken()?->getUser();
        $session = $event->getRequest()->getSession();

        // Solo limpiar si el token que se cierra es el vigente
        if ($user instanceof User && $user->getSessionToken() === $session->get(self::KEY)) {
            $user->setSessionToken(null);
            $this->em->flush();
        }
    }

    //    #[AsEventListener(event: RequestEvent::class)]
    public function onRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $user = $this->security->getUser();
        if (!$user instanceof User) {
            return;
        }

        /** @var Session $session */
        $session = $event->getRequest()->getSession();

        if ($session->get(self::KEY) !== $user->getSessionToken()) {
            $this->security->logout(false); // false = no CSRF check
            $session->getFlashBag()->add('danger', 'Tu sesión se cerró porque iniciaste sesión en otro dispositivo.');
            $event->setResponse(new RedirectResponse($this->urls->generate('app_login')));
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            LoginSuccessEvent::class => ['onLogin'],
            LogoutEvent::class => ['onLogout'],
            RequestEvent::class => ['onRequest'],
        ];
    }
}
