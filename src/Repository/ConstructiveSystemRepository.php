<?php

namespace App\Repository;

use App\Entity\ConstructiveSystem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ConstructiveSystem>
 */
class ConstructiveSystemRepository extends ServiceEntityRepository implements FilterInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ConstructiveSystem::class);
    }

    public function addFilter(QueryBuilder $builder, string $filter, bool $place = true): void
    {
        // TODO: Implement addFilter() method.
    }
}
