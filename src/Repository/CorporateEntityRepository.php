<?php

namespace App\Repository;

use App\Entity\CorporateEntity;
use App\Entity\Enums\CorporateEntityType;
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
    public function findEntities(string $filter = '', int $amountPerPage = 10, int $page = 1, string $type = ''): Paginator
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
}
