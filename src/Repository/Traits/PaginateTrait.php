<?php

namespace App\Repository\Traits;

use Doctrine\ORM\Query;
use Doctrine\ORM\Tools\Pagination\Paginator;

trait PaginateTrait
{
    /**
     * @param Query $dql
     * @param int $page
     * @param int $limit
     * @return Paginator
     */
    private function paginate(Query $dql, int $page, int $limit): Paginator
    {
        $paginator = new Paginator($dql, false);
        $paginator->getQuery()
            ->setFirstResult($limit * ($page - 1)) // Offset
            ->setMaxResults($limit); // Limit

        return $paginator;
    }
}