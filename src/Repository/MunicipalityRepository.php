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
    public function findMunicipalities(string $filter = '', ?int $amountPerPage = 10, ?int $page = 1): Paginator
    {
        $builder = $this->createQueryBuilder('m')
            ->select(['m', 'p'])
            ->leftJoin('m.province', 'p');
        $this->addFilter($builder, $filter);
        $query = $builder->orderBy('m.name', 'ASC')->getQuery();

        return $this->paginate($query, $page, $amountPerPage);
    }

    /**
     * @return Paginator<mixed>
     */
    public function findAmountProject(string $filter = '', ?int $amountPerPage = 10, ?int $page = 1): Paginator
    {
        $dql = 'SELECT m.id AS id,
       m.name AS name,
       p.name AS province,
       count(DISTINCT i.id) AS investments,
       count(DISTINCT pr.id) AS projects,
       count(DISTINCT b.id) AS buildings
  FROM App\Entity\Municipality m
       LEFT JOIN
       App\Entity\Province p ON m.province = p.id
       LEFT JOIN
       App\Entity\Investment i ON m.id = i.municipality
       LEFT JOIN
       App\Entity\Project pr ON pr.investment = i.id
       LEFT JOIN
       App\Entity\Building b ON b.project = pr.id';

        return $this->pagination($dql, $filter, $page, $amountPerPage);
    }

    /**
     * @return Paginator<mixed>
     */
    public function findAmountClients(string $filter = '', ?int $amountPerPage = 10, ?int $page = 1): Paginator
    {
        $dql = 'SELECT m.id AS id,
       m.name AS name,
       p.name AS province,
       count(DISTINCT ic.id) AS individual,
       count(DISTINCT ec.id) AS enterprise
  FROM App\Entity\Municipality m
       LEFT JOIN
       App\Entity\Province p ON m.province = p.id
       LEFT JOIN
       App\Entity\Client c ON m.id = c.municipality
       LEFT JOIN
       App\Entity\IndividualClient ic ON c.id = ic.id
       LEFT JOIN
       App\Entity\EnterpriseClient ec ON c.id = ec.id';

        return $this->pagination($dql, $filter, $page, $amountPerPage);
    }

    public function pagination(string $dql, string $filter, ?int $page, ?int $amountPerPage): Paginator
    {
        $parameters = [];
        if ('' !== $filter) {
            $dql .= ' WHERE m.name LIKE :filter OR p.name LIKE :filter';
            $parameters['filter'] = '%'.$filter.'%';
        }

        $dql .= ' GROUP BY m.id ORDER BY m.name';

        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameters($parameters);

        return $this->paginate($query, $page, $amountPerPage);
    }
}
