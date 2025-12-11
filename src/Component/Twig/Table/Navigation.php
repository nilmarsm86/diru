<?php

namespace App\Component\Twig\Table;

use App\DTO\Paginator;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(template: 'component/twig/table/navigation.html.twig')]
final class Navigation
{
    public int $page = 1;
    public string $queryName = 'page';
    /** @var array<int|string> */
    public array $queryStrings = [];
    public ?Paginator $paginator = null;
    public string $path = '';

    public function __construct(private readonly RequestStack $requestStack, private readonly RouterInterface $router)
    {
    }

    /**
     * cuando se monta por primera vez el componete.
     */
    public function mount(): void
    {
        $route = $this->requestStack->getMainRequest()?->attributes->get('_route');
        $this->path = is_string($route) ? $route : '';
    }

    /**
     * Generate path in number route.
     */
    public function getNumberPath(int $item): string
    {
        $this->queryStrings[$this->queryName] = $item;
        $routeParams = (array) $this->requestStack->getMainRequest()?->attributes->get('_route_params');

        return $this->router->generate($this->path, $routeParams + $this->queryStrings);
    }
}
