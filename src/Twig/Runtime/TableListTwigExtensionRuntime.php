<?php

namespace App\Twig\Runtime;

use Symfony\Component\HttpFoundation\Request;
use Twig\Extension\RuntimeExtensionInterface;

class TableListTwigExtensionRuntime implements RuntimeExtensionInterface
{
    /**
     * Show reload in table list.
     */
    public function showReload(Request $request): bool
    {
        $filter = (string) $request->query->get('filter', '');
        $amountPerPage = (int) $request->query->get('amount', '10');
        $pageNumber = (int) $request->query->get('page', '1');

        return '' !== $filter || 10 !== $amountPerPage || 1 !== $pageNumber;
    }

    public function th(Request $request, int $position, string $queryName, string $columnName): string
    {
        return $this->cell($request, $position, $queryName, $columnName, 'th');
    }

    public function td(Request $request, int $position, string $queryName, string $columnName): string
    {
        return $this->cell($request, $position, $queryName, $columnName, 'td');
    }

    public function cell(Request $request, int $position, string $queryName, string $columnName, string $type): string
    {
        $haystack = (array) json_decode(urldecode($request->query->get($queryName, '')));
        if (0 === count($haystack)) {
            return (('td' === $type) ? '<td>' : '<th>').$columnName.(('td' === $type) ? '</td>' : '</th>');
        }

        if (!in_array((string) $position, $haystack, true)) {
            return (('td' === $type) ? '<td>' : '<th>').$columnName.(('td' === $type) ? '</td>' : '</th>');
        }

        return '';
    }
}
