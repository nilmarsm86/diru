<?php

namespace App\Repository;

use App\Entity\Project;
use App\Entity\ProjectUrbanRegulation;
use App\Repository\Traits\PaginateTrait;
use App\Repository\Traits\SaveData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ProjectUrbanRegulation>
 */
class ProjectUrbanRegulationRepository extends ServiceEntityRepository implements FilterInterface
{
    use SaveData;
    use PaginateTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProjectUrbanRegulation::class);
    }

    public function addFilter(QueryBuilder $builder, string $filter, bool $place = true): void
    {
        // TODO: Implement addFilter() method.
    }

    /**
     * @return Paginator<mixed>
     */
    public function findUrbanRegulationsInProject(Project $project, string $filter = '', int $amountPerPage = 10, int $page = 1): Paginator
    {
        $builder = $this->createQueryBuilder('pur');
        //        $this->addFilter($builder, $filter);
        $builder->andWhere('pur.project  = :project');
        $builder->setParameter('project', $project->getId());
        $query = $builder->orderBy('pur.id', 'ASC')->getQuery();

        return $this->paginate($query, $page, $amountPerPage);
    }
}
