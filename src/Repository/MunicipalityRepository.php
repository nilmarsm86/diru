<?php

namespace App\Repository;

use App\Entity\Municipality;
use App\Repository\Traits\PaginateTrait;
use App\Repository\Traits\SaveData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception;
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
     * @return array<mixed>
     *
     * @throws Exception
     */
    public function findAmountProject(string $filter = '', ?int $amountPerPage = 10, ?int $page = 1): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = $this->sqlAmountProject($filter, $amountPerPage, $page);

        return $conn->executeQuery($sql, [
            'page' => (((int) $page - 1) * (int) $amountPerPage),
            'amount' => $amountPerPage,
            'filter' => $filter,
        ])->fetchAllAssociative();
    }

    private function sqlAmountProject(string $filter = '', ?int $amountPerPage = 10, ?int $page = 1): string
    {
        $sql = 'SELECT m.id AS id,
       m.name AS name,
       p.name as province,
       count(DISTINCT i.id) AS investments,
       count(DISTINCT pr.id) AS projects,
       count(DISTINCT b.id) AS buildings
  FROM municipality m
       LEFT JOIN
       province p ON m.province_id = p.id
       LEFT JOIN
       investment i ON m.id = i.municipality_id
       LEFT JOIN
       project pr ON pr.investment_id = i.id
       LEFT JOIN
       building b ON b.project_id = pr.id';

        if ('' !== $filter) {
            $sql .= ' WHERE m.name LIKE :filter OR p.name LIKE :filter';
        }

        $sql .= ' GROUP BY m.id ORDER BY m.name';

        if (null !== $amountPerPage && null !== $page) {
            $sql .= ' LIMIT :page,:amount';
        }

        return $sql;
    }

    /**
     * @return array<mixed>
     *
     * @throws Exception
     */
    public function findAmountClients(string $filter = '', ?int $amountPerPage = 10, ?int $page = 1): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = $this->sqlAmountClients($filter, $amountPerPage, $page);

        return $conn->executeQuery($sql, [
            'page' => (((int) $page - 1) * (int) $amountPerPage),
            'amount' => $amountPerPage,
            'filter' => $filter,
        ])->fetchAllAssociative();
    }

    private function sqlAmountClients(string $filter = '', ?int $amountPerPage = 10, ?int $page = 1): string
    {
        $sql = 'SELECT m.id AS id,
       m.name AS name,
       p.name AS province,
       count(DISTINCT ic.id) AS individual,
       count(DISTINCT ec.id) AS enterprise
  FROM municipality m
       LEFT JOIN
       province p ON m.province_id = p.id
       LEFT JOIN
       client c ON m.id = c.municipality_id
       LEFT JOIN
       individual_client ic ON c.id = ic.id
       LEFT JOIN
       enterprise_client ec ON c.id = ec.id';

        if ('' !== $filter) {
            $sql .= ' WHERE m.name LIKE :filter OR p.name LIKE :filter';
        }

        $sql .= ' GROUP BY m.id ORDER BY m.name';

        if (null !== $amountPerPage && null !== $page) {
            $sql .= ' LIMIT :page,:amount';
        }

        return $sql;
    }
}
