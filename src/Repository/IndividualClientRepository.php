<?php

namespace App\Repository;

use App\Entity\IndividualClient;
use App\Repository\Traits\PaginateTrait;
use App\Repository\Traits\SaveData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<IndividualClient>
 */
class IndividualClientRepository extends ServiceEntityRepository
{
    use SaveData;
    use PaginateTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, IndividualClient::class);
    }

    //    /**
    //     * @return IndividualClient[] Returns an array of IndividualClient objects
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

    //    public function findOneBySomeField($value): ?IndividualClient
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
            $predicate = "p.name LIKE :filter ";
            $predicate .= "OR p.identificationNumber LIKE :filter ";
            $predicate .= "OR p.passport LIKE :filter ";
            $predicate .= "OR ic.phone LIKE :filter ";
            $predicate .= "OR ic.email LIKE :filter ";
            if ($place) {
                $predicate .= "OR mun.name LIKE :filter ";
                $predicate .= "OR pro.name LIKE :filter ";
            }

            $builder->andWhere($predicate)
                ->setParameter(':filter', '%' . $filter . '%');
        }
    }

    /**
     * @param string $filter
     * @param int $amountPerPage
     * @param int $page
     * @return Paginator Returns an array of User objects
     */
    public function findIndividuals(string $filter = '', int $amountPerPage = 10, int $page = 1): Paginator
    {
        $builder = $this->createQueryBuilder('ic')->select(['ic', 'mun', 'pro', 'p'])
            ->innerJoin('ic.municipality', 'mun')
            ->leftJoin('mun.province', 'pro')
            ->leftJoin('ic.person', 'p');
//        $this->addType($builder, $type);
        $this->addFilter($builder, $filter);
        $query = $builder->orderBy('ic.id', 'ASC')->getQuery();
        return $this->paginate($query, $page, $amountPerPage);
    }
}
