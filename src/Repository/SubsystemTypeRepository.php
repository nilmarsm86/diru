<?php

namespace App\Repository;

use App\Entity\Enums\SubsystemFunctionalClassification;
use App\Entity\SubsystemType;
use App\Repository\Traits\PaginateTrait;
use App\Repository\Traits\SaveData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use Exception;

/**
 * @extends ServiceEntityRepository<SubsystemType>
 *
 * @method SubsystemType|null find($id, $lockMode = null, $lockVersion = null)
 * @method SubsystemType|null findOneBy(mixed[] $criteria, mixed[] $orderBy = null)
 * @method SubsystemType[]    findAll()
 * @method SubsystemType[]    findBy(mixed[] $criteria, mixed[] $orderBy = null, $limit = null, $offset = null)
 */
class SubsystemTypeRepository extends ServiceEntityRepository implements FilterInterface
{
    use SaveData;
    use PaginateTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SubsystemType::class);
    }

//    /**
//     * @return SubsystemType[] Returns an array of SubsystemType objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?SubsystemType
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

    public function addFilter(QueryBuilder $builder, string $filter, bool $place = true): void
    {
        if ($filter) {
            $predicate = "sst.name LIKE :filter ";
            $builder->andWhere($predicate)
                ->setParameter(':filter', '%' . $filter . '%');
        }
    }

    private function addClassification(QueryBuilder $builder, string $classification): void
    {
        if ($classification !== '') {
            $classification = SubsystemFunctionalClassification::from($classification);
            $builder->andWhere("sst.classification = :classification ")->setParameter(':classification', $classification);
        }
    }

    /**
     * @param string $filter
     * @param int $amountPerPage
     * @param int $page
     * @param string $classification
     * @return Paginator<mixed>
     */
    public function findSubsystemsType(string $filter = '', int $amountPerPage = 10, int $page = 1, string $classification = ''): Paginator
    {
        $builder = $this->createQueryBuilder('sst');
        $this->addClassification($builder, $classification);
        $this->addFilter($builder, $filter);
        $builder->orderBy('sst.classification', 'ASC');
        $query = $builder->addOrderBy('sst.name', 'ASC')->getQuery();

        return $this->paginate($query, $page, $amountPerPage);
    }

    /**
     * @param SubsystemType $entity
     * @param bool $flush
     * @return void
     * @throws Exception
     */
    public function remove(SubsystemType $entity, bool $flush = false): void
    {
        if ($entity->getSubsystemTypeSubsystemSubTypes()->count() > 0) {
            throw new Exception('El tipo de subsistema aun tiene sub tipos asociados.', 1);
        }

        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->flush();
        }
    }

    public function findSubsystemTypeForForm(?SubsystemFunctionalClassification $subsystemFunctionalClassification): QueryBuilder
    {
        return $this->createQueryBuilder('sst')
            ->where("sst.classification = :classification")
            ->setParameter('classification', $subsystemFunctionalClassification?->value)
            ->orderBy('sst.name');
    }
}
