<?php

namespace App\Repository;

use App\Entity\LocationZone;
use App\Repository\Interfaces\FilterInterface;
use App\Repository\Traits\PaginateTrait;
use App\Repository\Traits\SaveData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LocationZone>
 */
class LocationZoneRepository extends ServiceEntityRepository implements FilterInterface
{
    use SaveData;
    use PaginateTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LocationZone::class);
    }

    public function addFilter(QueryBuilder $builder, string $filter, bool $place = true): void
    {
        if ('' !== $filter) {
            $predicate = 'lz.name LIKE :filter ';
            $builder->andWhere($predicate)
                ->setParameter(':filter', '%'.$filter.'%');
        }
    }

    /**
     * @return Paginator<mixed>
     */
    public function findLocationZones(string $filter = '', ?int $amountPerPage = 10, ?int $page = 1): Paginator
    {
        $builder = $this->createQueryBuilder('lz');
        $this->addFilter($builder, $filter);
        $query = $builder->orderBy('lz.name', 'ASC')->getQuery();

        return $this->paginate($query, $page, $amountPerPage);
    }

    /**
     * @return Paginator<mixed>
     */
    public function findAmountProject(string $filter = '', ?int $amountPerPage = 10, ?int $page = 1): Paginator
    {
        $builder = $this->createQueryBuilder('lz')
            ->select(
                'lz.id AS id',
                'lz.name AS name',
                'COUNT(DISTINCT i.id) AS investments',
                'COUNT(DISTINCT pr.id) AS projects',
                'COUNT(DISTINCT b.id) AS buildings'
            )
            ->leftJoin('App\Entity\Investment', 'i', 'ON', 'lz.id = i.locationZone')
            ->leftJoin('App\Entity\Project', 'pr', 'ON', 'pr.investment = i.id')
            ->leftJoin('App\Entity\Building', 'b', 'ON', 'b.project = pr.id');

        $this->addFilter($builder, $filter);
        $query = $builder->groupBy('lz.id')->orderBy('lz.name', 'ASC')->getQuery();

        return $this->paginate($query, $page, $amountPerPage);
    }
}
