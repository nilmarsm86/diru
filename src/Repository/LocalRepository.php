<?php

namespace App\Repository;

use App\Entity\Building;
use App\Entity\Floor;
use App\Entity\Local;
use App\Entity\SubSystem;
use App\Repository\Traits\PaginateTrait;
use App\Repository\Traits\SaveData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Local>
 */
class LocalRepository extends ServiceEntityRepository
{
    use SaveData;
    use PaginateTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Local::class);
    }

    //    /**
    //     * @return Local[] Returns an array of Local objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('l')
    //            ->andWhere('l.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('l.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Local
    //    {
    //        return $this->createQueryBuilder('l')
    //            ->andWhere('l.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function addFilter(QueryBuilder $builder, string $filter, bool $place = true): void
    {
        if ($filter) {
            $predicate = "l.name LIKE :filter ";
            $builder->andWhere($predicate)
                ->setParameter(':filter', '%' . $filter . '%');
        }
    }

    /**
     * @param SubSystem $subSystem
     * @param string $filter
     * @param int $amountPerPage
     * @param int $page
     * @param bool $reply
     * @return Paginator Returns an array of User objects
     */
    public function findSubSystemLocals(SubSystem $subSystem, string $filter = '', int $amountPerPage = 10, int $page = 1, bool $reply = false): Paginator
    {
        $builder = $this->createQueryBuilder('l')->select(['l', 'ss'])
            ->leftJoin('l.subSystem', 'ss')
            ->andWhere('ss.id = :idSubSystem');
//            ->andWhere('l.original IS NULL');
        //TODO: tener en cuenta cuando es un subsistema nuevo dentro de la planta
        $dqlReply = ($reply) ? 'l.hasReply = false AND (l.state = 3 OR l.state = 0)' : 'l.original IS NULL AND (l.hasReply IS NULL OR l.hasReply = true) AND l.state != 3';
        $builder->andWhere($dqlReply);
        $builder->setParameter(':idSubSystem', $subSystem->getId());
        $this->addFilter($builder, $filter, false);
        $query = $builder->orderBy('l.number', 'ASC')
            ->getQuery();
        return $this->paginate($query, $page, $amountPerPage);
    }
}
