<?php

namespace App\Repository;

use App\Entity\Organism;
use App\Repository\Interfaces\FilterInterface;
use App\Repository\Traits\PaginateTrait;
use App\Repository\Traits\SaveData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Organism>
 */
class OrganismRepository extends ServiceEntityRepository implements FilterInterface
{
    use SaveData;
    use PaginateTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Organism::class);
    }

    public function addFilter(QueryBuilder $builder, string $filter, bool $place = true): void
    {
        if ('' !== $filter) {
            $predicate = 'o.name LIKE :filter ';
            $builder->andWhere($predicate)
                ->setParameter(':filter', '%'.$filter.'%');
        }
    }

    /**
     * @return Paginator<mixed>
     */
    public function findOrganisms(string $filter = '', ?int $amountPerPage = 10, ?int $page = 1): Paginator
    {
        $builder = $this->createQueryBuilder('o');
        $this->addFilter($builder, $filter, false);
        $query = $builder->orderBy('o.name', 'ASC')->getQuery();

        return $this->paginate($query, $page, $amountPerPage);
    }

    /**
     * @return Paginator<mixed>
     */
    public function findByCorporateEntityType(string $filter = '', ?int $amountPerPage = 10, ?int $page = 1): Paginator
    {
        $builder = $this->createQueryBuilder('o')
            ->select(
                'o.id AS id',
                'o.name AS name',
                "SUM(CASE WHEN ce.type = '0' THEN 1 ELSE 0 END) AS client",
                "SUM(CASE WHEN ce.type = '1' THEN 1 ELSE 0 END) AS constructor",
                "SUM(CASE WHEN ce.type = '2' THEN 1 ELSE 0 END) AS client_constructor",
                "SUM(CASE WHEN ce.type = '3' THEN 1 ELSE 0 END) AS draftman",
                'COUNT(ce.id) AS total'
            )
            ->leftJoin('App\Entity\CorporateEntity', 'ce', 'ON', 'o.id = ce.organism');

        $this->addFilter($builder, $filter);
        $query = $builder->groupBy('o.id')->orderBy('o.name', 'ASC')->getQuery();

        return $this->paginate($query, $page, $amountPerPage);
    }

    /**
     * @return Paginator<mixed>
     */
    public function findByEnterpriseClient(string $filter = '', ?int $amountPerPage = 10, ?int $page = 1): Paginator
    {
        $builder = $this->createQueryBuilder('o')
            ->select(
                'o.id AS id',
                'o.name AS name',
                'COUNT(ec.id) AS enterprise_client'
            )
            ->leftJoin('App\Entity\CorporateEntity', 'ce', 'ON', 'o.id = ce.organism')
            ->leftJoin('App\Entity\EnterpriseClient', 'ec', 'ON', 'ce.id = ec.corporateEntity');

        $this->addFilter($builder, $filter);
        $query = $builder->groupBy('o.id')->orderBy('o.name', 'ASC')->getQuery();

        return $this->paginate($query, $page, $amountPerPage);
    }
}
