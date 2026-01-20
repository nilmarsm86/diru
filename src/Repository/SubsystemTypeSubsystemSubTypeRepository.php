<?php

namespace App\Repository;

use App\Entity\SubsystemTypeSubsystemSubType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SubsystemTypeSubsystemSubType>
 */
class SubsystemTypeSubsystemSubTypeRepository extends ServiceEntityRepository implements FilterInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SubsystemTypeSubsystemSubType::class);
    }

    public function addFilter(QueryBuilder $builder, string $filter, bool $place = true): void
    {
        // TODO: Implement addFilter() method.
    }
}
