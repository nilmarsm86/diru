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
 * @extends ServiceEntityRepository<SubsystemSubType>
 *
 * @method SubsystemSubType|null find($id, $lockMode = null, $lockVersion = null)
 * @method SubsystemSubType|null findOneBy(mixed[] $criteria, mixed[] $orderBy = null)
 * @method SubsystemSubType[]    findAll()
 * @method SubsystemSubType[]    findBy(mixed[] $criteria, mixed[] $orderBy = null, $limit = null, $offset = null)
 */
class SubsystemSubTypeRepository extends ServiceEntityRepository implements FilterInterface
{
    use SaveData;
    use PaginateTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SubsystemSubType::class);
    }

    public function addFilter(QueryBuilder $builder, string $filter, bool $place = true): void
    {
        if ('' !== $filter) {
            $predicate = 'ssst.name LIKE :filter ';
            //            $predicate .= "OR sst.name LIKE :filter ";
            $builder->andWhere($predicate)
                ->setParameter(':filter', '%'.$filter.'%');
        }
    }

    /**
     * @return Paginator<mixed>
     */
    public function findSubsystemSubtypes(string $filter = '', int $amountPerPage = 10, int $page = 1): Paginator
    {
        $builder = $this->createQueryBuilder('ssst');
        //            ->select(['ssst', 'sst'])
        //            ->leftJoin('ssst.subsystemTypes', 'sst');
        $this->addFilter($builder, $filter);
        $query = $builder->orderBy('ssst.name', 'ASC')->getQuery();

        return $this->paginate($query, $page, $amountPerPage);
    }

    /**
     * @throws \Exception
     */
    public function remove(SubsystemSubType $entity, bool $flush = false): void
    {
        if ($entity->getSubsystemTypeSubsystemSubTypes()->count() > 0) {
            foreach ($entity->getSubsystemTypeSubsystemSubTypes() as $subsystemTypeSubsystemSubTypes) {
                if ($subsystemTypeSubsystemSubTypes->getSubSystems()->count() > 0) {
                    throw new \Exception('El subtipo de subsistema aun esta en algunos tipos de subsistema.', 1);
                }
            }
        }

        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->flush();
        }
    }
}
