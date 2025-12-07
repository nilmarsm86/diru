<?php

namespace App\Repository;

use App\Entity\EnterpriseClient;
use App\Repository\Traits\PaginateTrait;
use App\Repository\Traits\SaveData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use Exception;

/**
 * @extends ServiceEntityRepository<EnterpriseClient>
 */
class EnterpriseClientRepository extends ServiceEntityRepository implements FilterInterface
{
    use SaveData;
    use PaginateTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EnterpriseClient::class);
    }

//    /**
//     * @return EnterpriseClient[] Returns an array of EnterpriseClient objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('e.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?EnterpriseClient
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

    public function addFilter(QueryBuilder $builder, string $filter, bool $place = true): void
    {
        if ($filter) {
            $predicate = "r.name LIKE :filter ";
            $predicate .= "OR r.lastname LIKE :filter ";
            $predicate .= "OR r.identificationNumber LIKE :filter ";
            $predicate .= "OR r.passport LIKE :filter ";
            $predicate .= "OR ec.phone LIKE :filter ";
            $predicate .= "OR ec.email LIKE :filter ";
            $predicate .= "OR ce.name LIKE :filter ";
            if ($place) {
                $predicate .= "OR mun.name LIKE :filter ";
                $predicate .= "OR pro.name LIKE :filter ";
            }

            $builder->andWhere($predicate)
                ->setParameter(':filter', '%' . $filter . '%');
        }
    }

    /**
     * @param string $filter
     * @param int $amountPerPage
     * @param int $page
     * @return Paginator Returns an array of User objects
     */
    public function findEnterprises(string $filter = '', int $amountPerPage = 10, int $page = 1): Paginator
    {
        $builder = $this->createQueryBuilder('ec')->select(['ec', 'mun', 'pro', 'r', 'ce'])
            ->innerJoin('ec.municipality', 'mun')
            ->leftJoin('mun.province', 'pro')
            ->leftJoin('ec.representative', 'r')
            ->leftJoin('ec.corporateEntity', 'ce');
        $this->addFilter($builder, $filter);
        $query = $builder->orderBy('ec.id', 'ASC')->getQuery();
        return $this->paginate($query, $page, $amountPerPage);
    }

    /**
     * @param EnterpriseClient $entity
     * @param bool $flush
     * @return void
     * @throws Exception
     */
    public function remove(EnterpriseClient $entity, bool $flush = false): void
    {
        if($entity->hasProjects()){
            throw new Exception('Este cliente empresarial aun tiene proyectos asociados.', 1);
        }

        //Si el el cliente no tiene proyectos asociados puede ser eliminado
//        if(!is_null($entity->getCorporateEntity())){
//            throw new Exception('Este cliente empresarial tiene una entidad corporativa asociada.', 1);
//        }

        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->flush();
        }
    }
}
