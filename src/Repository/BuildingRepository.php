<?php

namespace App\Repository;

use App\Entity\Building;
use App\Repository\Traits\PaginateTrait;
use App\Repository\Traits\SaveData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Building>
 */
class BuildingRepository extends ServiceEntityRepository
{
    use SaveData;
    use PaginateTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Building::class);
    }

    //    /**
    //     * @return Building[] Returns an array of Building objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('b')
    //            ->andWhere('b.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('b.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Building
    //    {
    //        return $this->createQueryBuilder('b')
    //            ->andWhere('b.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function addFilter(QueryBuilder $builder, string $filter, bool $place = true): void
    {
        if($filter){
            $predicate = "b.name LIKE :filter ";
            $predicate .= "OR c.name LIKE :filter ";
//            $predicate .= "OR c.code LIKE :filter ";
//            $predicate .= "OR c.country LIKE :filter ";
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
    public function findBuildings(string $filter = '', int $amountPerPage = 10, int $page = 1): Paginator
    {
        $builder = $this->createQueryBuilder('b')->select(['b', 'c'])
            ->innerJoin('b.constructor', 'c');
        $this->addFilter($builder, $filter, false);
        $query = $builder->orderBy('b.name', 'ASC')->getQuery();
        return $this->paginate($query, $page, $amountPerPage);
    }
}
