<?php

namespace App\Component\Twig\Card;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(template: 'component/twig/card/refresh.html.twig')]
final class Refresh
{
    public string $path = '';
    /** @var array<string> */
    public array $queryNames = [];

    public function __construct(private readonly RequestStack $requestStack)
    {

    }

    public function showRefresh(): bool
    {
        foreach ($this->queryNames as $q) {
            $currentRequest = $this->requestStack->getCurrentRequest();
            if ($currentRequest?->query->has($q) && strlen((string)$currentRequest->query->get($q)) > 0) {
                return true;
            }
        }

        return false;
    }
}
