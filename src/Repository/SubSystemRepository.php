<?php

namespace App\Repository;

use App\Entity\Building;
use App\Entity\Floor;
use App\Entity\SubSystem;
use App\Repository\Traits\PaginateTrait;
use App\Repository\Traits\SaveData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use Exception;

/**
 * @extends ServiceEntityRepository<SubSystem>
 */
class SubSystemRepository extends ServiceEntityRepository
{
    use SaveData;
    use PaginateTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SubSystem::class);
    }

//    /**
//     * @return SubSystem[] Returns an array of SubSystem objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('f.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Floor
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

    public function addFilter(QueryBuilder $builder, string $filter, bool $place = true): void
    {
        if($filter){
            $predicate = "ss.name LIKE :filter ";
            $builder->andWhere($predicate)
                ->setParameter(':filter','%'.$filter.'%');
        }
    }

    /**
     * @param Floor $floor
     * @param string $filter
     * @param int $amountPerPage
     * @param int $page
     * @param bool $reply
     * @return Paginator Returns an array of User objects
     */
    public function findSubsystemsFloor(Floor $floor, string $filter = '', int $amountPerPage = 10, int $page = 1, bool $reply = false): Paginator
    {
        $builder = $this->createQueryBuilder('ss')->select(['ss', 'f'])
            ->leftJoin('ss.floor', 'f')
            ->andWhere('f.id = :idFloor');
        //TODO: tener en cuenta cuando es un subsistema nuevo dentro de la planta
        $dqlReply = ($reply) ? 'ss.hasReply = false AND (ss.state = 3 OR ss.state = 0)' : 'ss.original IS NULL AND (ss.hasReply IS NULL OR ss.hasReply = true) AND ss.state != 3';//TODO: 3 Estado de estructura: Replica
        $builder->andWhere($dqlReply);
        $builder->setParameter(':idFloor', $floor->getId());
        $this->addFilter($builder, $filter, false);
        $query = $builder->addOrderBy('ss.name', 'ASC')
            ->getQuery();
        return $this->paginate($query, $page, $amountPerPage);
    }

    /**
     * @param Floor $entity
     * @param bool $flush
     * @return void
     * @throws Exception
     */
    public function remove(SubSystem $entity, bool $flush = false): void
    {
        if($entity->hasSubSystems()){
            throw new Exception('El subsistema aun tiene locales asociados. Elimine los mismos primero.', 1);
        }

        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->flush();
        }
    }
}
