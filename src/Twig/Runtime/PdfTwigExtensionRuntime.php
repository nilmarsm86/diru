<?php

namespace App\Twig\Runtime;

use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Extension\RuntimeExtensionInterface;

readonly class PdfTwigExtensionRuntime implements RuntimeExtensionInterface
{
    public function __construct(private RequestStack $requestStack)
    {
    }

    public function th(int $position, string $queryName, string $columnName, string $icon = ''): string
    {
        return $this->cell($position, $queryName, $columnName, $icon, 'th');
    }

    public function td(int $position, string $queryName, string $columnName, string $icon = ''): string
    {
        return $this->cell($position, $queryName, $columnName, $icon, 'td');
    }

    public function cell(int $position, string $queryName, string $columnName, string $icon, string $type): string
    {
        $request = $this->requestStack->getCurrentRequest();
        $url = $request?->query->get($queryName, '') ?? '';
        $haystack = (array) json_decode(urldecode($url));

        if (0 === count($haystack) || !in_array((string) $position, $haystack, true)) {
            return (('td' === $type) ? '<td>' : '<th>').$columnName.' '.$icon.(('td' === $type) ? '</td>' : '</th>');
        }

        return '';
    }
}
