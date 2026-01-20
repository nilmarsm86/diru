<?php

namespace App\Repository;

use App\Entity\IndividualClient;
use App\Repository\Traits\PaginateTrait;
use App\Repository\Traits\SaveData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<IndividualClient>
 */
class IndividualClientRepository extends ServiceEntityRepository implements FilterInterface
{
    use SaveData;
    use PaginateTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, IndividualClient::class);
    }

    public function addFilter(QueryBuilder $builder, string $filter, bool $place = true): void
    {
        if ('' !== $filter) {
            $predicate = 'p.name LIKE :filter ';
            $predicate .= 'OR p.lastname LIKE :filter ';
            $predicate .= 'OR p.identificationNumber LIKE :filter ';
            $predicate .= 'OR p.passport LIKE :filter ';
            $predicate .= 'OR ic.phone LIKE :filter ';
            $predicate .= 'OR ic.email LIKE :filter ';
            if ($place) {
                $predicate .= 'OR mun.name LIKE :filter ';
                $predicate .= 'OR pro.name LIKE :filter ';
            }

            $builder->andWhere($predicate)
                ->setParameter(':filter', '%'.$filter.'%');
        }
    }

    /**
     * @return Paginator<mixed>
     */
    public function findIndividuals(string $filter = '', int $amountPerPage = 10, int $page = 1): Paginator
    {
        $builder = $this->createQueryBuilder('ic')->select(['ic', 'mun', 'pro', 'p'])
            ->innerJoin('ic.municipality', 'mun')
            ->leftJoin('mun.province', 'pro')
            ->leftJoin('ic.person', 'p');
        //        $this->addType($builder, $type);
        $this->addFilter($builder, $filter);
        $query = $builder->orderBy('ic.id', 'ASC')->getQuery();

        return $this->paginate($query, $page, $amountPerPage);
    }

    /**
     * @throws \Exception
     */
    public function remove(IndividualClient $entity, bool $flush = false): void
    {
        if ($entity->hasProjects()) {
            throw new \Exception('Este cliente natural aun tiene proyectos asociados.', 1);
        }

        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->flush();
        }
    }
}
