<?php

namespace App\Repository;

use App\Entity\CorporateEntity;
use App\Entity\Enums\CorporateEntityType;
use App\Repository\Interfaces\FilterInterface;
use App\Repository\Traits\PaginateTrait;
use App\Repository\Traits\SaveData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CorporateEntity>
 */
class CorporateEntityRepository extends ServiceEntityRepository implements FilterInterface
{
    use SaveData;
    use PaginateTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CorporateEntity::class);
    }

    public function addFilter(QueryBuilder $builder, string $filter, bool $place = true): void
    {
        if ('' !== $filter) {
            $predicate = 'ce.name LIKE :filter ';
            $predicate .= 'OR o.name LIKE :filter ';
            if ($place) {
                $predicate .= 'OR mun.name LIKE :filter ';
                $predicate .= 'OR pro.name LIKE :filter ';
            }

            $builder->andWhere($predicate)
                ->setParameter(':filter', '%'.$filter.'%');
        }
    }

    private function addType(QueryBuilder $builder, string $type): void
    {
        if ('' !== $type) {
            $type = CorporateEntityType::from($type);
            $builder->andWhere('ce.type = :type ')->setParameter(':type', $type);
        }
    }

    /**
     * @return Paginator<mixed>
     */
    public function findEntities(string $filter = '', ?int $amountPerPage = 10, ?int $page = 1, string $type = ''): Paginator
    {
        $builder = $this->createQueryBuilder('ce')->select(['ce', 'mun', 'pro', 'o'])
            ->innerJoin('ce.municipality', 'mun')
            ->leftJoin('mun.province', 'pro')
            ->leftJoin('ce.organism', 'o');
        $this->addType($builder, $type);
        $this->addFilter($builder, $filter);
        $query = $builder->orderBy('ce.id', 'ASC')->getQuery();

        return $this->paginate($query, $page, $amountPerPage);
    }

    /**
     * @throws \Exception
     */
    public function remove(CorporateEntity $entity, bool $flush = false): void
    {
        if ($entity->hasEnterpriseClients()) {
            throw new \Exception('Esta entidad corporativa aun tiene clientes empresariales asociados.', 1);
        }

        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->flush();
        }
    }

    /**
     * @return Paginator<mixed>
     */
    public function findByType(string $filter = '', ?int $amountPerPage = 10, ?int $page = 1): Paginator
    {
        $builder = $this->createQueryBuilder('ce')
            ->select(
                'ce.id AS id',
                'ce.name AS name',
                "SUM(CASE WHEN ce.type = '0' THEN 1 ELSE 0 END) AS client",
                "SUM(CASE WHEN ce.type = '1' THEN 1 ELSE 0 END) AS constructor",
                "SUM(CASE WHEN ce.type = '2' THEN 1 ELSE 0 END) AS client_constructor",
                "SUM(CASE WHEN ce.type = '3' THEN 1 ELSE 0 END) AS draftman",
                'COUNT(ce.id) AS total'
            );

        $this->addFilter($builder, $filter);
        $query = $builder->groupBy('ce.id')->orderBy('ce.name', 'ASC')->getQuery();

        return $this->paginate($query, $page, $amountPerPage);
    }

    /**
     * @return Paginator<mixed>
     */
    public function findAmountProjectAndBuildings(string $filter = '', ?int $amountPerPage = 10, ?int $page = 1): Paginator
    {
        $builder = $this->createQueryBuilder('ce')
            ->select(
                'ce.id AS id',
                'ce.name AS name',
                'COUNT(DISTINCT p.id) AS projects',
                'COUNT(DISTINCT b.id) AS buildings'
            )
            ->leftJoin('App\Entity\EnterpriseClient', 'ec', 'ON', 'ce.id = ec.corporateEntity')
            ->leftJoin('App\Entity\Project', 'p', 'ON', 'p.client = ec.id')
            ->leftJoin('App\Entity\Building', 'b', 'ON', 'b.project = p.id');

        $this->addFilter($builder, $filter);
        $query = $builder->groupBy('ce.id')->orderBy('ce.name', 'ASC')->getQuery();

        return $this->paginate($query, $page, $amountPerPage);
    }
}
