<?php

namespace App\Repository;

use App\Entity\UrbanRegulation;
use App\Entity\UrbanRegulationType;
use App\Repository\Traits\PaginateTrait;
use App\Repository\Traits\SaveData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UrbanRegulation>
 */
class UrbanRegulationRepository extends ServiceEntityRepository implements FilterInterface
{
    use SaveData;
    use PaginateTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UrbanRegulation::class);
    }

    public function addFilter(QueryBuilder $builder, string $filter, bool $place = true): void
    {
        if ('' !== $filter) {
            $predicate = 'ur.code LIKE :filter ';
            $builder->andWhere($predicate)
                ->setParameter(':filter', '%'.$filter.'%');
        }
    }

    private function addType(QueryBuilder $builder, string $type): void
    {
        if ('' !== $type) {
            $builder->andWhere('urt.name = :type ')->setParameter(':type', $type);
        }
    }

    private function addStructure(QueryBuilder $builder, string $structure): void
    {
        if ('' !== $structure) {
            $builder->andWhere('ur.structure = :structure ')->setParameter(':structure', $structure);
        }
    }

    /**
     * @return Paginator<mixed>
     */
    public function findUrbanRegulations(string $filter = '', int $amountPerPage = 10, int $page = 1, string $type = '', string $structure = ''): Paginator
    {
        $builder = $this->createQueryBuilder('ur')
            ->leftJoin('ur.type', 'urt');
        $this->addType($builder, $type);
        $this->addStructure($builder, $structure);
        $this->addFilter($builder, $filter);
        $query = $builder->orderBy('ur.code', 'ASC')->getQuery();

        return $this->paginate($query, $page, $amountPerPage);
    }

    public function findUrbanRegulationForForm(?UrbanRegulationType $urbanRegulationType): QueryBuilder
    {
        $builder = $this->createQueryBuilder('ur')
            ->leftJoin('ur.type', 'urt');
        $this->addType($builder, null === $urbanRegulationType ? '' : $urbanRegulationType->getName());
        $builder->orderBy('ur.code', 'ASC')->getQuery();

        return $builder;
    }
}
