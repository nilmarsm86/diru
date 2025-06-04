<?php

namespace App\Repository\Traits;

trait SaveData
{
    /**
     * @param object $entity
     * @param bool $flush
     * @return void
     */
    public function save(object $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->flush();
        }
    }

    /**
     * @param object $entity
     * @param bool $flush
     * @return void
     */
    public function remove(object $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->flush();
        }
    }

    /**
     * @return void
     */
    public function flush(): void
    {
        $this->getEntityManager()->flush();
    }
}