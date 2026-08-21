<?php

namespace App\Repository;

use App\Entity\Province;
use App\Repository\Interfaces\FilterInterface;
use App\Repository\Interfaces\UbicationInterface;
use App\Repository\Traits\PaginateTrait;
use App\Repository\Traits\SaveData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Province>
 *
 * @method Province|null find($id, $lockMode = null, $lockVersion = null)
 * @method Province|null findOneBy(mixed[] $criteria, mixed[] $orderBy = null)
 * @method Province[]    findAll()
 * @method Province[]    findBy(mixed[] $criteria, mixed[] $orderBy = null, $limit = null, $offset = null)
 */
class ProvinceRepository extends ServiceEntityRepository implements FilterInterface, UbicationInterface
{
    use SaveData;
    use PaginateTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Province::class);
    }

    public function addFilter(QueryBuilder $builder, string $filter, bool $place = true): void
    {
        if ('' !== $filter) {
            $predicate = 'p.name LIKE :filter ';
            $builder->andWhere($predicate)
                ->setParameter(':filter', '%'.$filter.'%');
        }
    }

    /**
     * @return Paginator<mixed>
     */
    public function findProvinces(string $filter = '', ?int $amountPerPage = 10, ?int $page = 1): Paginator
    {
        $builder = $this->createQueryBuilder('p');
        $this->addFilter($builder, $filter);
        $query = $builder->orderBy('p.name', 'ASC')->getQuery();

        return $this->paginate($query, $page, $amountPerPage);
    }

    /**
     * @throws \Exception
     */
    public function remove(Province $entity, bool $flush = false): void
    {
        if ($entity->getMunicipalities()->count() > 0) {
            throw new \Exception('La provincia aun tiene municipios asociados.', 1);
        }

        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->flush();
        }
    }

    public function findProvincesForForm(): QueryBuilder
    {
        return $this->createQueryBuilder('p')
            ->where("p.name NOT LIKE '%provincia%'")
            ->orderBy('p.name');
    }

    /**
     * @return Paginator<mixed>
     */
    public function findAmountProject(string $filter = '', ?int $amountPerPage = 10, ?int $page = 1): Paginator
    {
        $builder = $this->createQueryBuilder('p')
            ->select(
                'p.id AS id',
                'p.name AS name',
                'COUNT(DISTINCT i.id) AS investments',
                'COUNT(DISTINCT pr.id) AS projects',
                'COUNT(DISTINCT b.id) AS buildings'
            )
            ->leftJoin('App\Entity\Municipality', 'm', 'ON', 'm.province = p.id')
            ->leftJoin('App\Entity\Investment', 'i', 'ON', 'm.id = i.municipality')
            ->leftJoin('App\Entity\Project', 'pr', 'ON', 'pr.investment = i.id')
            ->leftJoin('App\Entity\Building', 'b', 'ON', 'b.project = pr.id');

        $this->addFilter($builder, $filter);
        $query = $builder->groupBy('p.id')->orderBy('p.name', 'ASC')->getQuery();

        return $this->paginate($query, $page, $amountPerPage);
    }

    /**
     * @return Paginator<mixed>
     */
    public function findAmountClients(string $filter = '', ?int $amountPerPage = 10, ?int $page = 1): Paginator
    {
        $builder = $this->createQueryBuilder('p')
            ->select(
                'p.id AS id',
                'p.name AS name',
                'COUNT(DISTINCT ic.id) AS individual',
                'COUNT(DISTINCT ec.id) AS enterprise'
            )
            ->leftJoin('App\Entity\Municipality', 'm', 'ON', 'm.province = p.id')
            ->leftJoin('App\Entity\Client', 'c', 'ON', 'm.id = c.municipality')
            ->leftJoin('App\Entity\IndividualClient', 'ic', 'ON', 'c.id = ic.id')
            ->leftJoin('App\Entity\EnterpriseClient', 'ec', 'ON', 'c.id = ec.id');

        $this->addFilter($builder, $filter);
        $query = $builder->groupBy('p.id')->orderBy('p.name', 'ASC')->getQuery();

        return $this->paginate($query, $page, $amountPerPage);
    }
}
