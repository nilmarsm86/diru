<?php

namespace App\Repository;

use App\Entity\Investment;
use App\Repository\Traits\PaginateTrait;
use App\Repository\Traits\SaveData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Investment>
 */
class InvestmentRepository extends ServiceEntityRepository implements FilterInterface
{
    use SaveData;
    use PaginateTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Investment::class);
    }

    //    /**
    //     * @return Investment[] Returns an array of Investment objects
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

    //    public function findOneBySomeField($value): ?Investment
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
        if ($filter) {
            $predicate = 'i.name LIKE :filter ';
            $predicate .= 'OR i.betweenStreets LIKE :filter ';
            $predicate .= 'OR i.town LIKE :filter ';
            $predicate .= 'OR i.popularCouncil LIKE :filter ';
            $predicate .= 'OR i.district LIKE :filter ';
            $predicate .= 'OR i.street LIKE :filter ';
            if ($place) {
                $predicate .= 'OR mun.name LIKE :filter ';
                $predicate .= 'OR pro.name LIKE :filter ';
            }
            $builder->andWhere($predicate)
                ->setParameter(':filter', '%'.$filter.'%');
        }
    }

    /**
     * @return Paginator<mixed>
     */
    public function findInvestments(string $filter = '', int $amountPerPage = 10, int $page = 1): Paginator
    {
        $builder = $this->createQueryBuilder('i')->select(['i', 'mun', 'pro'])
            ->innerJoin('i.municipality', 'mun')
            ->leftJoin('mun.province', 'pro');
        $this->addFilter($builder, $filter);
        $query = $builder->orderBy('i.name', 'ASC')->getQuery();

        return $this->paginate($query, $page, $amountPerPage);
    }

    /**
     * @throws \Exception
     */
    public function remove(Investment $entity, bool $flush = false): void
    {
        if ($entity->hasProjects()) {
            throw new \Exception('La inversión aun tiene proyectos asociados.', 1);
        }

        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->flush();
        }
    }
}
