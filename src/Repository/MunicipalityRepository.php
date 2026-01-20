<?php

namespace App\Repository;

use App\Entity\Municipality;
use App\Repository\Traits\PaginateTrait;
use App\Repository\Traits\SaveData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Municipality>
 *
 * @method Municipality|null find($id, $lockMode = null, $lockVersion = null)
 * @method Municipality|null findOneBy(mixed[] $criteria, mixed[] $orderBy = null)
 * @method Municipality[]    findAll()
 * @method Municipality[]    findBy(mixed[] $criteria, mixed[] $orderBy = null, $limit = null, $offset = null)
 */
class MunicipalityRepository extends ServiceEntityRepository implements FilterInterface
{
    use SaveData;
    use PaginateTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Municipality::class);
    }

    public function addFilter(QueryBuilder $builder, string $filter, bool $place = true): void
    {
        if ('' !== $filter) {
            $predicate = 'm.name LIKE :filter ';
            if ($place) {
                $predicate .= 'OR p.name LIKE :filter ';
            }

            $builder->andWhere($predicate)
                ->setParameter(':filter', '%'.$filter.'%');
        }
    }

    /**
     * @return Paginator<mixed>
     */
    public function findMunicipalities(string $filter = '', int $amountPerPage = 10, int $page = 1): Paginator
    {
        $builder = $this->createQueryBuilder('m')
            ->select(['m', 'p'])
            ->leftJoin('m.province', 'p');
        $this->addFilter($builder, $filter);
        $query = $builder->orderBy('m.name', 'ASC')->getQuery();

        return $this->paginate($query, $page, $amountPerPage);
    }
}
