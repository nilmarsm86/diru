<?php

namespace App\Repository;

use App\Entity\ConstructiveAction;
use App\Entity\Enums\ConstructiveActionType;
use App\Entity\NetworkConnection;
use App\Repository\Traits\PaginateTrait;
use App\Repository\Traits\SaveData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ConstructiveAction>
 */
class ConstructiveActionRepository extends ServiceEntityRepository implements FilterInterface
{
    use SaveData;
    use PaginateTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ConstructiveAction::class);
    }

    public function addFilter(QueryBuilder $builder, string $filter, bool $place = true): void
    {
        if ('' !== $filter) {
            $predicate = 'ca.name LIKE :filter ';
            $builder->andWhere($predicate)
                ->setParameter(':filter', '%'.$filter.'%');
        }
    }

    private function addType(QueryBuilder $builder, string $type): void
    {
        if ('' !== $type) {
            $type = ConstructiveActionType::from($type);
            $builder->andWhere('ca.type = :type ')->setParameter(':type', $type);
        }
    }

    /**
     * @return Paginator<mixed>
     */
    public function findConstructiveActions(string $filter = '', int $amountPerPage = 10, int $page = 1, string $type = ''): Paginator
    {
        $builder = $this->createQueryBuilder('ca');
        $this->addType($builder, $type);
        $this->addFilter($builder, $filter);
        $query = $builder->orderBy('ca.name', 'ASC')->getQuery();

        return $this->paginate($query, $page, $amountPerPage);
    }

    /*
     * @throws \Exception
     */
    //    public function remove(NetworkConnection $entity, bool $flush = false): void
    //    {
    //        if ($entity->isOnLand()) {
    //            throw new \Exception('La conexión de red esta asociada a una o varias obras.', 1);
    //        }
    //
    //        $this->getEntityManager()->remove($entity);
    //
    //        if ($flush) {
    //            $this->flush();
    //        }
    //    }
}
