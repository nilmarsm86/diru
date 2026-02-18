<?php

namespace App\Entity\Traits;

use App\Entity\Client;
use App\Entity\EnterpriseClient;
use App\Entity\IndividualClient;
use App\Repository\EnterpriseClientRepository;
use App\Repository\IndividualClientRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

trait ClientTrait
{
    #[ORM\ManyToOne(inversedBy: 'projects')]
    #[Assert\Valid]
    private ?Client $client = null;

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function isIndividualClient(IndividualClientRepository $individualClientRepository): bool
    {
        if (is_null($this->getId())) {
            return true;
        }

        $client = $this->getClient();
        if (!is_null($client)) {
            $individual = $individualClientRepository->find($client->getId());

            return !is_null($individual);
        }

        return false;
    }

    public function getIndividualClient(IndividualClientRepository $individualClientRepository): ?IndividualClient
    {
        $client = $this->getClient();
        if (!is_null($client)) {
            return $individualClientRepository->find($client->getId());
        }

        return null;
    }

    public function isEnterpriseClient(EnterpriseClientRepository $enterpriseClientRepository): bool
    {
        $client = $this->getClient();
        if (!is_null($client)) {
            $enterprise = $enterpriseClientRepository->find($client->getId());

            return !is_null($enterprise);
        }

        return false;
    }

    public function getEnterpriseClient(EnterpriseClientRepository $enterpriseClientRepository): ?EnterpriseClient
    {
        $client = $this->getClient();
        if (!is_null($client)) {
            return $enterpriseClientRepository->find($client->getId());
        }

        return null;
    }

    public function setClient(?Client $client): static
    {
        $this->client = $client;

        return $this;
    }

    public function isFromEnterpriseClient(): bool
    {
        return $this->getClient() instanceof EnterpriseClient;
    }

    public function isFromIndividualClient(): bool
    {
        return $this->getClient() instanceof IndividualClient;
    }
}
