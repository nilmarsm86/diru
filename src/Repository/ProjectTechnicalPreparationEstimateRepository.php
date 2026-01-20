<?php

namespace App\Repository;

use App\Entity\ProjectTechnicalPreparationEstimate;
use App\Repository\Traits\PaginateTrait;
use App\Repository\Traits\SaveData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ProjectTechnicalPreparationEstimate>
 */
class ProjectTechnicalPreparationEstimateRepository extends ServiceEntityRepository implements FilterInterface
{
    use SaveData;
    use PaginateTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProjectTechnicalPreparationEstimate::class);
    }

    public function addFilter(QueryBuilder $builder, string $filter, bool $place = true): void
    {
        if ('' !== $filter) {
            $predicate = 'o.concept LIKE :filter ';
            $builder->andWhere($predicate)
                ->setParameter(':filter', '%'.$filter.'%');
        }
    }

    /**
     * @return Paginator<mixed>
     */
    public function findProjectTechnicalPreparationEstimate(string $filter = '', int $amountPerPage = 10, int $page = 1): Paginator
    {
        $builder = $this->createQueryBuilder('ptpe');
        $this->addFilter($builder, $filter, false);
        $query = $builder->orderBy('ptpe.concept', 'ASC')->getQuery();

        return $this->paginate($query, $page, $amountPerPage);
    }
}
