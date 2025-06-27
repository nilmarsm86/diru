<?php

namespace App\Repository;

use App\Entity\NetworkConnection;
use App\Entity\Project;
use App\Repository\Traits\PaginateTrait;
use App\Repository\Traits\SaveData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use Exception;

/**
 * @extends ServiceEntityRepository<NetworkConnection>
 */
class NetworkConnectionRepository extends ServiceEntityRepository
{
    use SaveData;
    use PaginateTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NetworkConnection::class);
    }

    //    /**
    //     * @return NetworkConnection[] Returns an array of NetworkConnection objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('n')
    //            ->andWhere('n.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('n.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?NetworkConnection
    //    {
    //        return $this->createQueryBuilder('n')
    //            ->andWhere('n.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    private function addFilter(QueryBuilder $builder, string $filter): void
    {
        if($filter){
            $predicate = "nc.name LIKE :filter ";
            $builder->andWhere($predicate)
                ->setParameter(':filter','%'.$filter.'%');
        }
    }

    /**
     * @param string $filter
     * @param int $amountPerPage
     * @param int $page
     * @return Paginator Returns an array of User objects
     */
    public function findNetworkConnections(string $filter = '', int $amountPerPage = 10, int $page = 1): Paginator
    {
        $builder = $this->createQueryBuilder('nc');
        $this->addFilter($builder, $filter);
        $query = $builder->orderBy('nc.name', 'ASC')->getQuery();
        return $this->paginate($query, $page, $amountPerPage);
    }

    /**
     * @param NetworkConnection $entity
     * @param bool $flush
     * @return void
     * @throws Exception
     */
    public function remove(NetworkConnection $entity, bool $flush = false): void
    {
        if($entity->isOnLand()){
            throw new Exception('La conexión de red esta asociada a una o varias obras.', 1);
        }

        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->flush();
        }
    }
}
