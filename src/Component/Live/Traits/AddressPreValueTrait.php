<?php

namespace App\Component\Live\Traits;

trait AddressPreValueTrait
{
    private function applyIntegerField(string $field): void
    {
        if (0 === $this->{$field}) {
            return;
        }

        $this->formValues[$field] = $this->{$field};
        $this->{$field} = 0;
    }

    private function applyStreet(): void
    {
        if ('' === $this->street) {
            return;
        }

        /** @var array<string, mixed> $streetAddress */
        $streetAddress = $this->formValues['streetAddress'] ?? [];
        $streetAddress['street'] = (string) $this->street;

        $this->formValues['streetAddress'] = $streetAddress;
        $this->street = '';
    }

    private function applyAddressField(string $field, int $value): void
    {
        if (0 === $value) {
            return;
        }

        /** @var array<string, mixed> $streetAddress */
        $streetAddress = $this->formValues['streetAddress'] ?? [];
        /** @var array<string, mixed> $address */
        $address = $streetAddress['address'] ?? [];
        $address[$field] = (string) $value;

        $streetAddress['address'] = $address;
        $this->formValues['streetAddress'] = $streetAddress;
        $this->{$field} = 0;
    }

    private function applyMunicipality(): void
    {
        if (0 !== $this->municipality) {
            $this->applyAddressField('municipality', $this->municipality);
            $this->municipality = 0;

            return;
        }

        $this->reconcileMunicipalityWithProvince();
    }

    private function reconcileMunicipalityWithProvince(): void
    {
        /** @var array<string, array<string, array<int, mixed>>> $formValues */
        $formValues = $this->formValues;
        $address = $formValues['streetAddress']['address'] ?? [];
        $provinceId = $address['province'] ?? null;

        if (null === $provinceId) {
            return;
        }

        $municipalityId = $address['municipality'] ?? null;

        if ((bool) $municipalityId) {
            $this->reconcileExistingMunicipality($municipalityId, $provinceId);

            return;
        }

        $this->setFirstMunicipalityOfProvince($provinceId);
    }

    private function reconcileExistingMunicipality(mixed $municipalityId, mixed $provinceId): void
    {
        $mun = $this->municipalityRepository->find($municipalityId);

        if ((string) $mun?->getProvince()?->getId() === $provinceId) {
            return;
        }

        $this->setFirstMunicipalityOfProvince($provinceId);
    }

    private function setFirstMunicipalityOfProvince(mixed $provinceId): void
    {
        $prov = $this->provinceRepository->find($provinceId);

        if (null === $prov) {
            return;
        }

        $municipalities = $prov->getMunicipalities();
        $first = $municipalities->count() > 0 ? $municipalities->first() : false;

        //        $this->formValues['streetAddress']['address']['municipality'] = false !== $first
        //            ? (string) $first->getId()
        //            : '';

        /** @var array<string, array<string, mixed>> $streetAddress */
        $streetAddress = $this->formValues['streetAddress'] ?? [];

        /** @var array<string, mixed> $address */
        $address = $streetAddress['address'] ?? [];

        $address['municipality'] = false !== $first ? (string) $first->getId() : '';

        $streetAddress['address'] = $address;
        $this->formValues['streetAddress'] = $streetAddress;
    }
}
