<?php

namespace App\Repository;

use App\Entity\Currency;
use App\Repository\Traits\PaginateTrait;
use App\Repository\Traits\SaveData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Currency>
 */
class CurrencyRepository extends ServiceEntityRepository implements FilterInterface
{
    use SaveData;
    use PaginateTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Currency::class);
    }

    public function addFilter(QueryBuilder $builder, string $filter, bool $place = true): void
    {
        if ('' !== $filter) {
            $predicate = 'c.name LIKE :filter ';
            $predicate .= 'OR c.code LIKE :filter ';
            $builder->andWhere($predicate)
                ->setParameter(':filter', '%'.$filter.'%');
        }
    }

    /**
     * @return Paginator<mixed>
     */
    public function findCurrencies(string $filter = '', int $amountPerPage = 10, int $page = 1): Paginator
    {
        $builder = $this->createQueryBuilder('c');
        $this->addFilter($builder, $filter);
        $query = $builder->orderBy('c.name', 'ASC')->getQuery();

        return $this->paginate($query, $page, $amountPerPage);
    }

    //    /**
    //     * @throws Exception
    //     */
    //    public function remove(Currency $entity, bool $flush = false): void
    //    {
    // //        if ($entity->getMunicipalities()->count() > 0) {
    // //            throw new Exception('La provincia aun tiene municipios asociados.', 1);
    // //        }
    //
    //        $this->getEntityManager()->remove($entity);
    //
    //        if ($flush) {
    //            $this->flush();
    //        }
    //    }
}
