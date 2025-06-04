<?php

namespace App\Repository;

use App\Entity\Constructor;
use App\Entity\EnterpriseClient;
use App\Repository\Traits\PaginateTrait;
use App\Repository\Traits\SaveData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use Exception;

/**
 * @extends ServiceEntityRepository<Constructor>
 */
class ConstructorRepository extends ServiceEntityRepository
{
    use SaveData;
    use PaginateTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Constructor::class);
    }

    //    /**
    //     * @return Constructor[] Returns an array of Constructor objects
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

    //    public function findOneBySomeField($value): ?Constructor
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function addFilter(QueryBuilder $builder, string $filter, bool $place = true): void
    {
        if($filter){
            $predicate = "c.name LIKE :filter ";
            $predicate .= "OR c.code LIKE :filter ";
            $predicate .= "OR c.country LIKE :filter ";
            $builder->andWhere($predicate)
                ->setParameter(':filter','%'.$filter.'%');
        }
    }

    /**
     * @param string $filter
     * @param int $amountPerPage
     * @param int $page
     * @return Paginator Returns an array of User objects
     */
    public function findConstructors(string $filter = '', int $amountPerPage = 10, int $page = 1): Paginator
    {
        $builder = $this->createQueryBuilder('c');
        $this->addFilter($builder, $filter, false);
        $query = $builder->orderBy('c.name', 'ASC')->getQuery();
        return $this->paginate($query, $page, $amountPerPage);
    }

    /**
     * @param Constructor $entity
     * @param bool $flush
     * @return void
     * @throws Exception
     */
    public function remove(Constructor $entity, bool $flush = false): void
    {
        if($entity->getBuildings()->count()){
            throw new Exception('Esta constructora aun tiene obras asociados.', 1);
        }

        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->flush();
        }
    }
}
