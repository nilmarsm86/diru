<?php

namespace App\Repository;

use Doctrine\ORM\QueryBuilder;

interface FilterInterface
{
    public function addFilter(QueryBuilder $builder, string $filter, bool $place = true): void;
}
