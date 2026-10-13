<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Solo la ventana de escritorio de Diru puede usar este servidor.
 *
 * El lanzador (Neutralino) genera un token aleatorio en cada arranque de PHP. Lo entrega en un
 * script generado (auto_prepend_file) que define DIRU_LAUNCH_TOKEN en $_SERVER/$_ENV/getenv antes
 * de cada petición, así que %env(DIRU_LAUNCH_TOKEN)% funciona igual que con una variable real.
 * El iframe entra por /__diru/auth?t=<token>; aquí
 * se canjea por una cookie HttpOnly y desde ese momento toda petición debe llevarla.
 *
 * Esto cierra el puerto local a: páginas web abiertas en el navegador normal del usuario
 * (CSRF y DNS rebinding contra 127.0.0.1), otros usuarios del equipo y cualquier otro
 * programa que escanee puertos. Requiere Symfony >= 6.1 (atributo #[Autowire]).
 *
 * Archivo: src/EventSubscriber/LaunchGuardSubscriber.php (se registra solo con autoconfigure).
 */
final class LaunchGuardSubscriber implements EventSubscriberInterface
{
    private const COOKIE = 'diru_launch';
    private const AUTH_PATH = '/__diru/auth';
    /** Solo GET, y la ruta debe devolver un cuerpo mínimo (sin datos). Lo usa el lanzador. */
    private const PUBLIC_PATHS = ['/ping'];

    public function __construct(
        #[Autowire('%env(default::DIRU_LAUNCH_TOKEN)%')]
        private readonly ?string $token,
        #[Autowire('%env(default::DIRU_PARENT_ORIGIN)%')]
        private readonly ?string $parentOrigin,
        #[Autowire('%kernel.environment%')]
        private readonly string $environment,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            // Antes del router (32) y del firewall de seguridad (8)
            KernelEvents::REQUEST => ['onRequest', 512],
            KernelEvents::RESPONSE => ['onResponse', -512],
        ];
    }

    public function onRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        if (null === $this->token || '' === $this->token) {
            // Sin lanzador (desarrollo en un navegador normal) solo se admite fuera de producción.
            // En producción falla cerrado: si el token no llegó, no se sirve nada.
            if ('prod' === $this->environment) {
                $event->setResponse($this->deny(Response::HTTP_SERVICE_UNAVAILABLE));
            }

            return;
        }

        $request = $event->getRequest();
        $path = $request->getPathInfo();

        if ($request->isMethod('GET') && \in_array($path, self::PUBLIC_PATHS, true)) {
            return;
        }

        if (self::AUTH_PATH === $path) {
            $given = $request->query->all()['t'] ?? null;
            if (!$request->isMethod('GET') || !\is_string($given) || !hash_equals($this->token, $given)) {
                $event->setResponse($this->deny());

                return;
            }

            $response = new RedirectResponse('/');
            $response->headers->setCookie(
                Cookie::create(self::COOKIE, $this->token, 0, '/', null, false, true, false, Cookie::SAMESITE_LAX),
            );
            $response->headers->set('Cache-Control', 'no-store');
            $response->headers->set('Referrer-Policy', 'no-referrer');
            $event->setResponse($response);

            return;
        }

        $cookie = $request->cookies->get(self::COOKIE);
        if (!\is_string($cookie) || !hash_equals($this->token, $cookie)) {
            $event->setResponse($this->deny());
        }
    }

    public function onResponse(ResponseEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $headers = $event->getResponse()->headers;
        $headers->set('X-Content-Type-Options', 'nosniff');
        if (!$headers->has('Referrer-Policy')) {
            $headers->set('Referrer-Policy', 'same-origin');
        }

        // Solo la ventana de escritorio puede incrustar la app (anti-clickjacking).
        // Si ya envías un Content-Security-Policy (p. ej. con Nelmio), añade ahí este mismo
        // frame-ancestors en vez de duplicar la cabecera.
        if (
            !$headers->has('Content-Security-Policy')
            && null !== $this->parentOrigin
            && 1 === preg_match('#^https?://[A-Za-z0-9.\-]+(:\d{1,5})?$#', $this->parentOrigin)
        ) {
            $headers->set('Content-Security-Policy', 'frame-ancestors '.$this->parentOrigin);
        }
    }

    private function deny(int $status = Response::HTTP_FORBIDDEN): Response
    {
        return new Response('Acceso denegado', $status, [
            'Content-Type' => 'text/plain; charset=utf-8',
            'Cache-Control' => 'no-store',
        ]);
    }
}
