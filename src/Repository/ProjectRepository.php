<?php

namespace App\Repository;

use App\Entity\Enums\ProjectState;
use App\Entity\Enums\ProjectType;
use App\Entity\Project;
use App\Repository\Traits\PaginateTrait;
use App\Repository\Traits\SaveData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Project>
 */
class ProjectRepository extends ServiceEntityRepository implements FilterInterface
{
    use SaveData;
    use PaginateTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Project::class);
    }

    //    /**
    //     * @return Project[] Returns an array of Project objects
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

    //    public function findOneBySomeField($value): ?Project
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
        if ('' !== $filter) {
            $predicate = 'p.name LIKE :filter ';
            $predicate .= 'OR i.name LIKE :filter ';
            //            $predicate .= "OR c.code LIKE :filter ";
            //            $predicate .= "OR c.country LIKE :filter ";
            $builder->andWhere($predicate)
                ->setParameter(':filter', '%'.$filter.'%');
        }
    }

    private function addType(QueryBuilder $builder, string $type): void
    {
        if ('' !== $type) {
            $type = ProjectType::from($type);
            $builder->andWhere('p.type = :type ')->setParameter(':type', $type);
        }
    }

    private function addState(QueryBuilder $builder, string $state): void
    {
        if ('' !== $state) {
            $state = ProjectState::from($state);
            $builder->andWhere('p.state = :state ')->setParameter(':state', $state);
        }
    }

    /**
     * @return Paginator<mixed>
     */
    public function findProjects(string $filter = '', int $amountPerPage = 10, int $page = 1, string $type = '', string $state = ''): Paginator
    {
        $builder = $this->createQueryBuilder('p')->select(['p', 'i'])
            ->innerJoin('p.investment', 'i');
        //            ->innerJoin('p.contract', 'c');
        $this->addType($builder, $type);
        $this->addState($builder, $state);
        $this->addFilter($builder, $filter, false);
        $query = $builder->orderBy('p.name', 'ASC')->getQuery();

        return $this->paginate($query, $page, $amountPerPage);
    }

    /**
     * @throws \Exception
     */
    public function remove(Project $entity, bool $flush = false): void
    {
        $msg = 'Si desea podría cambiarle el estado al proyecto a: Cancelado.';

        if (!is_null($entity->getClient())) {
            throw new \Exception('El proyecto aun tiene un cliente asociado. '.$msg, 1);
        }

        if ($entity->hasBuildings()) {
            throw new \Exception('El proyecto tiene obras asociadas. '.$msg, 1);
        }

        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->flush();
        }
    }
}
