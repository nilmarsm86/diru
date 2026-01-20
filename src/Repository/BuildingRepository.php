<?php

namespace App\Repository;

use App\Entity\Building;
use App\Entity\Enums\BuildingState;
use App\Entity\Project;
use App\Repository\Traits\PaginateTrait;
use App\Repository\Traits\SaveData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Building>
 */
class BuildingRepository extends ServiceEntityRepository implements FilterInterface
{
    use SaveData;
    use PaginateTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Building::class);
    }

    public function addFilter(QueryBuilder $builder, string $filter, bool $place = true): void
    {
        if ('' !== $filter) {
            $predicate = 'b.name LIKE :filter ';
            //            $predicate .= "OR c.name LIKE :filter ";
            //            $predicate .= "OR c.code LIKE :filter ";
            $predicate .= 'OR p.name LIKE :filter ';
            $builder->andWhere($predicate)
                ->setParameter(':filter', '%'.$filter.'%');
        }
    }

    /**
     * @return Paginator<mixed>
     */
    public function findBuildings(string $filter = '', int $amountPerPage = 10, int $page = 1): Paginator
    {
        $builder = $this->createQueryBuilder('b')->select(['b', 'p'])
            ->leftJoin('b.project', 'p');
        $this->addFilter($builder, $filter, false);
        $query = $builder->orderBy('b.name', 'ASC')->getQuery();

        return $this->paginate($query, $page, $amountPerPage);
    }

    private function addState(QueryBuilder $builder, string $state): void
    {
        if ('' !== $state) {
            $state = BuildingState::from($state);
            $builder->andWhere('b.state = :state ')->setParameter(':state', $state);
        }
    }

    /**
     * @return Paginator<mixed>
     */
    public function findBuildingsByProject(Project $project, string $filter = '', int $amountPerPage = 10, int $page = 1, string $state = ''): Paginator
    {
        $builder = $this->createQueryBuilder('b')->select(['b', 'p'])
            ->leftJoin('b.project', 'p')
            ->where('p.id = :project')
            ->setParameter(':project', $project->getId());
        $this->addState($builder, $state);
        $this->addFilter($builder, $filter, false);
        $query = $builder->orderBy('b.name', 'ASC')->getQuery();

        return $this->paginate($query, $page, $amountPerPage);
    }

    /**
     * @throws \Exception
     */
    public function remove(Building $entity, bool $flush = false): void
    {
        //        $msg = 'Si desea podría cambiarle el estado a la obra a: Cancelada.';
        //
        //        if (!is_null($entity->getProject())) {
        //            throw new \Exception('La obra aun esta asignada a un proyecto. '.$msg, 1);
        //        }
        //
        //        if ($entity->hasConstructor()) {
        //            throw new \Exception('La obra aun tiene una constructora a cargo. '.$msg, 1);
        //        }
        //
        //        if ($entity->hasDraftsman()) {
        //            throw new \Exception('La obra aun tiene un proyectista a cargo. '.$msg, 1);
        //        }
        //
        //        if ($entity->hasFloors()) {
        //            throw new \Exception('La obra aun tiene plantas asociadas. '.$msg, 1);
        //        }

        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->flush();
        }
    }
}
