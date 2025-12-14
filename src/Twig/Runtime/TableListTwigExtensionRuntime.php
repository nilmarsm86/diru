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
}
