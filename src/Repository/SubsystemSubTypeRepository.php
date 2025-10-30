<?php

namespace App\Repository;

use App\Entity\SubsystemSubType;
use App\Repository\Traits\PaginateTrait;
use App\Repository\Traits\SaveData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Municipality>
 *
 * @method SubsystemSubType|null find($id, $lockMode = null, $lockVersion = null)
 * @method SubsystemSubType|null findOneBy(array $criteria, array $orderBy = null)
 * @method SubsystemSubType[]    findAll()
 * @method SubsystemSubType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SubsystemSubTypeRepository extends ServiceEntityRepository implements FilterInterface
{
    use SaveData;
    use PaginateTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SubsystemSubType::class);
    }

//    /**
//     * @return SubsystemSubType[] Returns an array of SubsystemSubType objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('m.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Municipality
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
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
        if($filter){
            $predicate = "ssst.name LIKE :filter ";
            $predicate .= "OR sst.name LIKE :filter ";
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
    public function findSubsystemSubtypes(string $filter = '', int $amountPerPage = 10, int $page = 1): Paginator
    {
        $builder = $this->createQueryBuilder('ssst')
            ->select(['ssst', 'sst'])
            ->leftJoin('ssst.subsystemType', 'sst');
        $this->addFilter($builder, $filter);
//        $query = $builder->orderBy('sst.classification', 'ASC')->getQuery();
        $query = $builder->orderBy('sst.id', 'ASC')->getQuery();
        return $this->paginate($query, $page, $amountPerPage);
    }

}
