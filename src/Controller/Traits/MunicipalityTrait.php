<?php

namespace App\Controller\Traits;

use App\Entity\Municipality;
use Doctrine\ORM\EntityManagerInterface;

trait MunicipalityTrait
{
    public function findMunicipality(EntityManagerInterface $entityManager, ?int $municipalityId): Municipality
    {
        if (!is_null($municipalityId)) {
            return $entityManager->getRepository(Municipality::class)->find($municipalityId);
        } else {
            return $entityManager->getRepository(Municipality::class)->findOneBy(['name' => ucfirst('Sin municipio')]);
        }
    }

    /**
     * @param array<mixed> $data
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
