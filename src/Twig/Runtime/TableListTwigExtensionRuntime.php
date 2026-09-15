<?php

namespace App\Twig\Runtime;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Extension\RuntimeExtensionInterface;

class TableListTwigExtensionRuntime implements RuntimeExtensionInterface
{
    public function __construct(private readonly RequestStack $requestStack)
    {
    }

    /**
     * Show reload in table list.
     */
    public function showReload(Request $request): bool
    {
        $request = $this->requestStack->getCurrentRequest();
        $filter = (string) $request?->query->get('filter', '');
        $amountPerPage = (int) $request?->query->get('amount', '10');
        $pageNumber = (int) $request?->query->get('page', '1');

        return '' !== $filter || 10 !== $amountPerPage || 1 !== $pageNumber;
    }

    //    public function th(int $position, string $queryName, string $columnName, string $icon = ''): string
    //    {
    //        return $this->cell($position, $queryName, $columnName, $icon, 'th');
    //    }
    //
    //    public function td(int $position, string $queryName, string $columnName, string $icon = ''): string
    //    {
    //        return $this->cell($position, $queryName, $columnName, $icon, 'td');
    //    }
    //
    //    public function cell(int $position, string $queryName, string $columnName, string $icon, string $type): string
    //    {
    //        $request = $this->requestStack->getCurrentRequest();
    //        $haystack = (array) json_decode(urldecode($request->query->get($queryName, '')));
    //
    //        if (0 === count($haystack) || !in_array((string) $position, $haystack, true)) {
    //            return (('td' === $type) ? '<td>' : '<th>').$columnName.' '.$icon.(('td' === $type) ? '</td>' : '</th>');
    //        }
    //
    //        return '';
    //    }
}
