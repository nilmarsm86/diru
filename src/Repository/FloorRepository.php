<?php

namespace App\Repository;

use App\Entity\Building;
use App\Entity\Floor;
use App\Repository\Traits\PaginateTrait;
use App\Repository\Traits\SaveData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Floor>
 */
class FloorRepository extends ServiceEntityRepository implements FilterInterface
{
    use SaveData;
    use PaginateTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Floor::class);
    }

    public function addFilter(QueryBuilder $builder, string $filter, bool $place = true): void
    {
        if ('' !== $filter) {
            $predicate = 'f.name LIKE :filter ';
            $builder->andWhere($predicate)
                ->setParameter(':filter', '%'.$filter.'%');
        }
    }

    /**
     * @return Paginator<mixed>
     */
    public function findBuildingFloors(Building $building, string $filter = '', int $amountPerPage = 10, int $page = 1, bool $reply = false): Paginator
    {
        $builder = $this->createQueryBuilder('f')->select(['f', 'b'])
            ->leftJoin('f.building', 'b')
            ->andWhere('b.id = :idBuilding');
        $dqlReply = ($reply) ? 'f.hasReply = false AND (f.state = 3 OR f.state = 0)' : 'f.original IS NULL AND (f.hasReply IS NULL OR f.hasReply = true) AND f.state != 3'; // TODO: 3 Estado de estructura: Replica
        $builder->andWhere($dqlReply);

        $builder->setParameter(':idBuilding', $building->getId());
        $this->addFilter($builder, $filter, false);
        $query = $builder->orderBy('f.groundFloor', 'DESC')
            ->addOrderBy('f.position', 'ASC')
            ->addOrderBy('f.name', 'ASC')
            ->getQuery();

        return $this->paginate($query, $page, $amountPerPage);
    }

    /**
     * @throws \Exception
     */
    public function remove(Floor $entity, bool $flush = false): void
    {
        if ($entity->hasSubSystems()) {
            throw new \Exception('La planta aun tiene locales asociados. Elimine los mismos primero.', 1);
        }

        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->flush();
        }
    }

    //    /**
    //     * @param Building $building
    //     * @param string $filter
    //     * @param int $amountPerPage
    //     * @param int $page
    //     * @return Paginator<mixed>
    //     */
    //    public function findReplyBuildingFloors(Building $building, string $filter = '', int $amountPerPage = 10, int $page = 1): Paginator
    //    {
    //        $builder = $this->createQueryBuilder('f')->select(['f', 'b'])
    //            ->leftJoin('f.building', 'b')
    //            ->andWhere('b.id = :idBuilding')
    //            ->andWhere('f.original IS NOT NULL');
    //        $builder->setParameter(':idBuilding', $building->getId());
    //        $this->addFilter($builder, $filter, false);
    //        $query = $builder->orderBy('f.groundFloor', 'DESC')
    //            ->addOrderBy('f.position', 'ASC')
    //            ->addOrderBy('f.name', 'ASC')
    //            ->getQuery();
    //        return $this->paginate($query, $page, $amountPerPage);
    //    }
}
