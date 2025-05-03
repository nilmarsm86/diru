<?php

namespace App\Repository;

use Doctrine\ORM\QueryBuilder;

interface FilterInterface
{
    /**
     * @param QueryBuilder $builder
     * @param string $filter
     * @param bool $place
     * @return void
     */
    public function addFilter(QueryBuilder $builder, string $filter, bool $place = true): void;
}