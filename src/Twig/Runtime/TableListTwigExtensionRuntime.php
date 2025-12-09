<?php

namespace App\Twig\Runtime;

use Symfony\Component\HttpFoundation\Request;
use Twig\Extension\RuntimeExtensionInterface;

class TableListTwigExtensionRuntime implements RuntimeExtensionInterface
{
    /**
     * Show reload in table list
     * @param Request $request
     * @return bool
     */
    public function showReload(Request $request): bool
    {
        $filter = $request->query->get('filter', '');
        /** @var int $amountPerPage */
        $amountPerPage = $request->query->get('amount', '10');
        /** @var int $pageNumber */
        $pageNumber = $request->query->get('page', '1');

        return (!empty($filter) || $amountPerPage !== 10 || $pageNumber !== 1);
    }
}
