<?php

namespace App\Repository;

use App\Entity\ConstructiveAction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ConstructiveAction>
 */
class ConstructiveActionRepository extends ServiceEntityRepository implements FilterInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ConstructiveAction::class);
    }

    public function addFilter(QueryBuilder $builder, string $filter, bool $place = true): void
    {
        // TODO: Implement addFilter() method.
    }
}
