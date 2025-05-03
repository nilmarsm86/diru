<?php

namespace App\Component\Twig\Card;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(template: 'component/twig/card/refresh.html.twig')]
final class Refresh
{
    public string $path = '';
    public array $queryNames = [];

    public function __construct(private readonly RequestStack $requestStack)
    {

    }

    public function showRefresh(): bool
    {
        foreach ($this->queryNames as $q) {
            if ($this->requestStack->getCurrentRequest()->query->has($q)) {
                return true;
            }
        }

        return false;
    }
}
