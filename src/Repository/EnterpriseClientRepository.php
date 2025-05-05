<?php

namespace App\Repository;

use App\Entity\EnterpriseClient;
use App\Repository\Traits\PaginateTrait;
use App\Repository\Traits\SaveData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EnterpriseClient>
 */
class EnterpriseClientRepository extends ServiceEntityRepository
{
    use SaveData;
    use PaginateTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EnterpriseClient::class);
    }

//    /**
//     * @return EnterpriseClient[] Returns an array of EnterpriseClient objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('e.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?EnterpriseClient
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
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
    public function findEnterprises(string $filter = '', int $amountPerPage = 10, int $page = 1): Paginator
    {
        $builder = $this->createQueryBuilder('ec')->select(['ec', 'mun', 'pro', 'p', 'ce'])
            ->innerJoin('ec.municipality', 'mun')
            ->leftJoin('mun.province', 'pro')
            ->leftJoin('ec.person', 'p')
            ->leftJoin('ec.corporateEntity', 'ce');
        $this->addFilter($builder, $filter);
        $query = $builder->orderBy('ec.id', 'ASC')->getQuery();
        return $this->paginate($query, $page, $amountPerPage);
    }
}
