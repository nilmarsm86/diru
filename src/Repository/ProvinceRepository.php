<?php

namespace App\Repository;

use App\Entity\Province;
use App\Repository\Traits\PaginateTrait;
use App\Repository\Traits\SaveData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use Exception;

/**
 * @extends ServiceEntityRepository<Province>
 *
 * @method Province|null find($id, $lockMode = null, $lockVersion = null)
 * @method Province|null findOneBy(array $criteria, array $orderBy = null)
 * @method Province[]    findAll()
 * @method Province[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProvinceRepository extends ServiceEntityRepository
{
    use SaveData;
    use PaginateTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Province::class);
    }

//    /**
//     * @return Province[] Returns an array of Province objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Province
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

    private function addFilter(QueryBuilder $builder, string $filter): void
    {
        if($filter){
            $predicate = "p.name LIKE :filter ";
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
    public function findProvinces(string $filter = '', int $amountPerPage = 10, int $page = 1): Paginator
    {
        $builder = $this->createQueryBuilder('p');
        $this->addFilter($builder, $filter);
        $query = $builder->orderBy('p.name', 'ASC')->getQuery();
        return $this->paginate($query, $page, $amountPerPage);
    }

    /**
     * @param Province $entity
     * @param bool $flush
     * @return void
     * @throws Exception
     */
    public function remove(Province $entity, bool $flush = false): void
    {
        if($entity->getMunicipalities()->count()){
            throw new Exception('La provincia aun tiene municipios asociados.', 1);
        }

        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->flush();
        }
    }
}
