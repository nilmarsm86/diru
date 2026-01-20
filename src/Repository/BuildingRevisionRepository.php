<?php

namespace App\Repository;

use App\Entity\BuildingRevision;
use App\Repository\Traits\PaginateTrait;
use App\Repository\Traits\SaveData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BuildingRevision>
 */
class BuildingRevisionRepository extends ServiceEntityRepository
{
    use SaveData;
    use PaginateTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BuildingRevision::class);
    }

    /**
     * @return Paginator<mixed>
     */
    public function findRevisions(string $filter = '', int $amountPerPage = 10, int $page = 1): Paginator
    {
        $builder = $this->createQueryBuilder('br')->select(['b', 'p'])
            ->leftJoin('br.project', 'p');
        //        $this->addFilter($builder, $filter, false);
        $query = $builder->orderBy('b.name', 'ASC')->getQuery();

        return $this->paginate($query, $page, $amountPerPage);
    }
}
