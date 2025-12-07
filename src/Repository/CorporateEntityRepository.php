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
use Exception;

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

    //    /**
    //     * @return CorporateEntity[] Returns an array of CorporateEntity objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?CorporateEntity
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    /**
     * @inheritDoc
     */
    public function addFilter(QueryBuilder $builder, string $filter, bool $place = true): void
    {
        if ($filter) {
            $predicate = "ce.name LIKE :filter ";
            $predicate .= "OR o.name LIKE :filter ";
            if ($place) {
                $predicate .= "OR mun.name LIKE :filter ";
                $predicate .= "OR pro.name LIKE :filter ";
            }

            $builder->andWhere($predicate)
                ->setParameter(':filter', '%' . $filter . '%');
        }
    }

    private function addType(QueryBuilder $builder, $type): void
    {
        if ($type !== '') {
            $type = CorporateEntityType::from($type);
            $builder->andWhere("ce.type = :type ")->setParameter(':type', $type);
        }
    }

    /**
     * @param string $filter
     * @param int $amountPerPage
     * @param int $page
     * @param string $type
     * @return Paginator Returns an array of User objects
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
     * @param CorporateEntity $entity
     * @param bool $flush
     * @return void
     * @throws Exception
     */
    public function remove(CorporateEntity $entity, bool $flush = false): void
    {
        if($entity->hasEnterpriseClients()){
            throw new Exception('Esta entidad corporativa aun tiene clientes empresariales asociados.', 1);
        }

        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->flush();
        }
    }
}
