<?php

namespace App\Repository;

use App\Entity\Building;
use App\Entity\Enums\BuildingState;
use App\Entity\Enums\ProjectState;
use App\Entity\Investment;
use App\Entity\Project;
use App\Repository\Traits\PaginateTrait;
use App\Repository\Traits\SaveData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use Exception;

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
//            $predicate .= "OR c.name LIKE :filter ";
//            $predicate .= "OR c.code LIKE :filter ";
            $predicate .= "OR p.name LIKE :filter ";
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
        $builder = $this->createQueryBuilder('b')->select(['b', 'p'])
//            ->leftJoin('b.constructor', 'c')
            ->leftJoin('b.project', 'p');
        $this->addFilter($builder, $filter, false);
        $query = $builder->orderBy('b.name', 'ASC')->getQuery();
        return $this->paginate($query, $page, $amountPerPage);
    }

    private function addState(QueryBuilder $builder, $state): void
    {
        if ($state !== '') {
            $state = BuildingState::from($state);
            $builder->andWhere("b.state = :state ")->setParameter(':state', $state);
        }
    }

    /**
     * @param Project $project
     * @param string $filter
     * @param int $amountPerPage
     * @param int $page
     * @param string $state
     * @return Paginator Returns an array of User objects
     */
    public function findBuildingsByProject(Project $project, string $filter = '', int $amountPerPage = 10, int $page = 1, string $state = ''): Paginator
    {
        $builder = $this->createQueryBuilder('b')->select(['b', 'p'])
//            ->leftJoin('b.constructor', 'c')
            ->leftJoin('b.project', 'p')
        ->where('p.id = :project')
            ->setParameter(':project',$project->getId());
        $this->addState($builder, $state);
        $this->addFilter($builder, $filter, false);
        $query = $builder->orderBy('b.name', 'ASC')->getQuery();
        return $this->paginate($query, $page, $amountPerPage);
    }

    /**
     * @param Building $entity
     * @param bool $flush
     * @return void
     * @throws Exception
     */
    public function remove(Building $entity, bool $flush = false): void
    {
        $msg = 'Si desea podría cambiarle el estado a la obra a: Cancelada.';

        if(!is_null($entity->getProject())){
            throw new Exception('La obra aun esta asignada a un proyecto. '.$msg, 1);
        }

        if($entity->hasConstructor()){
            throw new Exception('La obra aun tiene una constructora a cargo. '.$msg, 1);
        }

        if($entity->hasDraftsman()){
            throw new Exception('La obra aun tiene un proyectista a cargo. '.$msg, 1);
        }

        if($entity->hasFloors()){
            throw new Exception('La obra aun tiene plantas asociadas. '.$msg, 1);
        }

        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->flush();
        }
    }
}
