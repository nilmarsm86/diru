<?php

namespace App\Repository;

use App\Entity\Ite;
use App\Repository\Traits\PaginateTrait;
use App\Repository\Traits\SaveData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Ite>
 */
class IteRepository extends ServiceEntityRepository
{
    use SaveData;
    use PaginateTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ite::class);
    }

    //    /**
    //     * @return Ite[] Returns an array of Ite objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('i')
    //            ->andWhere('i.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('i.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Ite
    //    {
    //        return $this->createQueryBuilder('i')
    //            ->andWhere('i.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function addFilter(QueryBuilder $builder, string $filter, bool $place = true): void
    {
        if ('' !== $filter) {
            $predicate = 'i.min LIKE :filter ';
            $predicate .= 'OR i.max LIKE :filter ';
            $predicate .= 'OR i.yearReference LIKE :filter ';
            //            if ($place) {
            //                $predicate .= 'OR c.name LIKE :filter ';
            //            }

            $builder->andWhere($predicate)
                ->setParameter(':filter', '%'.$filter.'%');
        }
    }

    /**
     * @return Paginator<mixed>
     */
    public function findItes(string $filter = '', int $amountPerPage = 10, int $page = 1): Paginator
    {
        $builder = $this->createQueryBuilder('i')
            ->select(['i', 'mu', 'ites', 'c', 'ipt'])
            ->leftJoin('i.measurementUnit', 'mu')
            ->leftJoin('i.source', 'ites')
            ->leftJoin('i.city', 'c')
            ->leftJoin('i.projectType', 'ipt');
        $this->addFilter($builder, $filter);
        $query = $builder->orderBy('i.yearReference', 'DESC')->getQuery();

        return $this->paginate($query, $page, $amountPerPage);
    }
}
