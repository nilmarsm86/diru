<?php

namespace App\Repository;

use App\Entity\Country;
use App\Repository\Traits\PaginateTrait;
use App\Repository\Traits\SaveData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Country>
 */
class CountryRepository extends ServiceEntityRepository implements FilterInterface
{
    use SaveData;
    use PaginateTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Country::class);
    }

    //    /**
    //     * @return Country[] Returns an array of Country objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Country
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function addFilter(QueryBuilder $builder, string $filter, bool $place = true): void
    {
        if ('' !== $filter) {
            $predicate = 'c.name LIKE :filter ';
            $builder->andWhere($predicate)
                ->setParameter(':filter', '%'.$filter.'%');
        }
    }

    /**
     * @return Paginator<mixed>
     */
    public function findCountries(string $filter = '', int $amountPerPage = 10, int $page = 1): Paginator
    {
        $builder = $this->createQueryBuilder('c');
        $this->addFilter($builder, $filter);
        $query = $builder->orderBy('c.name', 'ASC')->getQuery();

        return $this->paginate($query, $page, $amountPerPage);
    }

    /**
     * @throws \Exception
     */
    public function remove(Country $entity, bool $flush = false): void
    {
        if ($entity->getCities()->count() > 0) {
            throw new \Exception('El país aun tiene ciudades asociadas.', 1);
        }

        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->flush();
        }
    }

    public function findCountriesForForm(): QueryBuilder
    {
        return $this->createQueryBuilder('c')
//            ->where("c.name NOT LIKE '%country%'")
            ->orderBy('c.name');
    }
}
