<?php

namespace App\Controller\Traits;

use App\Entity\Equipment;
use App\Entity\Municipality;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

trait MunicipalityTrait
{
    /**
     * @param EntityManagerInterface $entityManager
     * @param int|null $municipalityId
     * @return Municipality
     */
    public function findMunicipality(EntityManagerInterface $entityManager, ?int $municipalityId): Municipality
    {
        if (!is_null($municipalityId)) {
            return $entityManager->getRepository(Municipality::class)->find($municipalityId);
        } else {
            return $entityManager->getRepository(Municipality::class)->findOneBy(['name' => ucfirst('Sin municipio')]);
        }
    }

    /**
     * @param object $entity
     * @param array $data
     * @return int
     */
    public function getMunicipalityId(object $entity, array $data): int
    {
        if ($data) {
            $address = $data['address'];
            $municipalityId = $address['municipality'] ?? null;
        } else {
            $municipalityId = $entity->getMunicipality()->getId();
        }

        return $municipalityId;
    }

}