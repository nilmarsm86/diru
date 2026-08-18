<?php

namespace App\Repository\Traits;

use Doctrine\ORM\Query;
use Doctrine\ORM\Tools\Pagination\Paginator;

trait PaginateTrait
{
    /**
     * @param Query<mixed, mixed> $dql
     *
     * @return Paginator<mixed>
     */
    private function paginate(Query $dql, ?int $page = null, ?int $limit = null): Paginator
    {
        $paginator = new Paginator($dql, false);
        if (null !== $page && null !== $limit) {
            $query = $paginator->getQuery();
            $query->setFirstResult($limit * ($page - 1)); // Offset
            $query->setMaxResults($limit); // Limit
        }

        return $paginator;
    }
}
