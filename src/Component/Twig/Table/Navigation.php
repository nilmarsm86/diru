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
    public array $queryStrings = [];
    public ?Paginator $paginator = null;
    public string $path = '';

    public function __construct(private readonly RequestStack $requestStack, private readonly RouterInterface $router)
    {

    }

    /**
     * cuando se monta por primera vez el componete
     * @return void
     */
    public function mount(): void
    {
        $this->path = $this->requestStack->getMainRequest()->attributes->get('_route');
    }

    /**
     * Generate path in number route
     * @param int $item
     * @return string
     */
    public function getNumberPath(int $item): string
    {
        $this->queryStrings[$this->queryName] = $item;
        return $this->router->generate($this->path, $this->queryStrings);
    }

}
