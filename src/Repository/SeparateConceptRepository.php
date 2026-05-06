<?php

namespace App\Repository;

use App\Entity\SeparateConcept;
use App\Repository\Traits\PaginateTrait;
use App\Repository\Traits\SaveData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SeparateConcept>
 */
class SeparateConceptRepository extends ServiceEntityRepository implements FilterInterface
{
    use SaveData;
    use PaginateTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SeparateConcept::class);
    }

    public function addFilter(QueryBuilder $builder, string $filter, bool $place = true): void
    {
        if ('' !== $filter) {
            $predicate = 'sc.name LIKE :filter ';
            $builder->andWhere($predicate)
                ->setParameter(':filter', '%'.$filter.'%');
        }
    }

    /**
     * @return array<mixed>
     */
    public function findSeparateConcepts(): array
    {
        /*$builder = $this->createQueryBuilder('sc')->select(['sc']);
        $this->addFilter($builder, $filter, false);
        $query = $builder->addOrderBy('sc.id', 'ASC')
            ->getQuery();

        return $this->paginate($query, $page, $amountPerPage);*/
        $rsm = new ResultSetMapping();
        $rsm->addEntityResult(SeparateConcept::class, 'c');

        $rsm->addFieldResult('c', 'id', 'id');
        //        $rsm->addFieldResult('c', 'parent_id', 'parentId');
        $rsm->addFieldResult('c', 'type', 'type');
        $rsm->addFieldResult('c', 'number', 'number');
        $rsm->addFieldResult('c', 'formula', 'formula');
        $rsm->addFieldResult('c', 'percent', 'percent');
        $rsm->addFieldResult('c', 'ignore_number', 'ignoreNumber');
        $rsm->addFieldResult('c', 'name', 'name');

        $sql = <<<SQL
        WITH RECURSIVE subtree AS (
            SELECT
                id, parent_id, type, number, formula, percent,
                ignore_number, name, 0 AS nivel
            FROM separate_concept

            UNION ALL

            SELECT
                t.id, t.parent_id, t.type, t.number, t.formula, t.percent,
                t.ignore_number, t.name, subtree.nivel + 1
            FROM separate_concept t
            INNER JOIN subtree ON t.parent_id = subtree.id
            WHERE subtree.nivel < 10
        )
        SELECT
            id, parent_id, type, number, formula, percent,
            ignore_number, name
        FROM subtree
        -- Sin ORDER BY en SQL: lo hacemos en PHP donde funciona igual en las dos BD
SQL;

        $query = $this->getEntityManager()->createNativeQuery($sql, $rsm);
        //        $query->setParameter('rootId', $rootId);

        /** @var SeparateConcept[] $result */
        $result = $query->getResult();

        // ORDEN NATURAL JERÁRQUICO PERFECTO (esto es lo que realmente necesitabas)
        usort($result, function (SeparateConcept $a, SeparateConcept $b): int {
            $partsA = array_map('intval', explode('.', ((bool) $a->getNumber()) ? $a->getNumber() : ''));
            $partsB = array_map('intval', explode('.', ((bool) $b->getNumber()) ? $b->getNumber() : ''));

            $maxLength = max(count($partsA), count($partsB));

            for ($i = 0; $i < $maxLength; ++$i) {
                $valA = $partsA[$i] ?? 0;
                $valB = $partsB[$i] ?? 0;

                if ($valA !== $valB) {
                    return $valA <=> $valB;
                }
            }

            return 0;
        });

        return $result;
    }

    /**
     * Devuelve el nodo padre + todos sus descendientes (hijos, nietos, bisnietos, tataranietos).
     *
     * @return SeparateConcept[]
     */
    //    public function findSubtree(int $rootId): array
    //    {
    //        $rsm = new ResultSetMapping();
    //        $rsm->addEntityResult(SeparateConcept::class, 'c');
    //
    //        // Mapea TODAS las columnas de tu tabla al nombre de las propiedades de la entidad
    //        $rsm->addFieldResult('c', 'id', 'id');
    // //        $rsm->addFieldResult('c', 'parent_id', 'parent');     // importante: usa el nombre de la propiedad en la entidad, NO la columna
    //        $rsm->addFieldResult('c', 'type', 'type');
    //        $rsm->addFieldResult('c', 'number', 'number');
    //        $rsm->addFieldResult('c', 'formula', 'formula');
    //        $rsm->addFieldResult('c', 'percent', 'percent');
    //        $rsm->addFieldResult('c', 'ignore_number', 'ignoreNumber'); // ajusta si tu propiedad se llama ignoreNumber o ignore_number
    //        $rsm->addFieldResult('c', 'name', 'name');
    //
    //        $sql = <<<SQL
    //            WITH RECURSIVE subtree AS (
    //                SELECT
    //                    id, parent_id, type, number, formula, percent, ignore_number, name, 0 AS nivel
    //                FROM separate_concept
    //                WHERE parent_id = :rootId
    //                UNION ALL
    //                SELECT
    //                    t.id, t.parent_id, t.type, t.number, t.formula, t.percent, t.ignore_number, t.name, subtree.nivel + 1
    //                FROM separate_concept t
    //                INNER JOIN subtree ON t.parent_id = subtree.id
    //                WHERE subtree.nivel < 4 -- protección aunque ya sabes que max 4 niveles
    //            )
    //            SELECT
    //                id, parent_id, type, number, formula, percent, ignore_number, name
    //            FROM subtree
    //            ORDER BY number;
    // SQL;
    //
    //        $query = $this->getEntityManager()->createNativeQuery($sql, $rsm);
    //        $query->setParameter('rootId', $rootId);
    //
    //        $result = $query->getResult();   // array de objetos SeparateConcept
    //
    //    }

    public function findSubtree(int $rootId): array
    {
        $rsm = new ResultSetMapping();
        $rsm->addEntityResult(SeparateConcept::class, 'c');

        $rsm->addFieldResult('c', 'id', 'id');
        //        $rsm->addFieldResult('c', 'parent_id', 'parentId');
        $rsm->addFieldResult('c', 'type', 'type');
        $rsm->addFieldResult('c', 'number', 'number');
        $rsm->addFieldResult('c', 'formula', 'formula');
        $rsm->addFieldResult('c', 'percent', 'percent');
        $rsm->addFieldResult('c', 'ignore_number', 'ignoreNumber');
        $rsm->addFieldResult('c', 'name', 'name');

        $sql = <<<SQL
        WITH RECURSIVE subtree AS (
            SELECT
                id, parent_id, type, number, formula, percent,
                ignore_number, name, 0 AS nivel
            FROM separate_concept
            WHERE id = :rootId

            UNION ALL

            SELECT
                t.id, t.parent_id, t.type, t.number, t.formula, t.percent,
                t.ignore_number, t.name, subtree.nivel + 1
            FROM separate_concept t
            INNER JOIN subtree ON t.parent_id = subtree.id
            WHERE subtree.nivel < 10
        )
        SELECT
            id, parent_id, type, number, formula, percent,
            ignore_number, name
        FROM subtree
        -- Sin ORDER BY en SQL: lo hacemos en PHP donde funciona igual en las dos BD
SQL;

        $query = $this->getEntityManager()->createNativeQuery($sql, $rsm);
        $query->setParameter('rootId', $rootId);

        /** @var SeparateConcept[] $result */
        $result = $query->getResult();

        // ORDEN NATURAL JERÁRQUICO PERFECTO (esto es lo que realmente necesitabas)
        usort($result, function (SeparateConcept $a, SeparateConcept $b): int {
            $partsA = array_map('intval', explode('.', ((bool) $a->getNumber()) ? $a->getNumber() : ''));
            $partsB = array_map('intval', explode('.', ((bool) $b->getNumber()) ? $b->getNumber() : ''));

            $maxLength = max(count($partsA), count($partsB));

            for ($i = 0; $i < $maxLength; ++$i) {
                $valA = $partsA[$i] ?? 0;
                $valB = $partsB[$i] ?? 0;

                if ($valA !== $valB) {
                    return $valA <=> $valB;
                }
            }

            return 0;
        });

        foreach($result as $key => $subtree) {
            if($subtree->getId() === $rootId) {
                unset($result[$key]);
            }
        }

        return $result;
    }
}
