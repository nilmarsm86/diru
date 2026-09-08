<?php

namespace App\Repository;

use App\Entity\Client;
use App\Entity\EnterpriseClient;
use App\Entity\IndividualClient;
use App\Entity\Representative;
use App\Repository\Interfaces\FilterInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

use function PHPUnit\Framework\assertIsArray;

/**
 * @extends ServiceEntityRepository<Client>
 */
class ClientRepository extends ServiceEntityRepository implements FilterInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Client::class);
    }

    public function addFilter(QueryBuilder $builder, string $filter, bool $place = true): void
    {
        // TODO: Implement addFilter() method.
    }

    public function countClients(): mixed
    {
        $builder = $this->createQueryBuilder('p')
            ->select('COUNT(DISTINCT ic.id) AS individual_client',
                'COUNT(DISTINCT r.id) AS representative',
                'COUNT(DISTINCT ec.id) AS enterprise_client',
            )
            ->from(IndividualClient::class, 'ic')
            ->from(EnterpriseClient::class, 'ec')
        ->from(Representative::class, 'r');

        $query = $builder->getQuery();

        $result = $query->getResult(AbstractQuery::HYDRATE_SCALAR);
        assertIsArray($result);

        return $result[0];
    }
}
