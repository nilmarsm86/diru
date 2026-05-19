<?php

declare(strict_types=1);

namespace App\Service\IteImport\Mapper;

use App\Entity\City;
use App\Entity\Country;
use App\Entity\Enums\IteQuality;
use App\Entity\Enums\IteType;
use App\Entity\Ite;
use App\Entity\IteProjectType;
use App\Entity\IteSource;
use App\Entity\MeasurementUnit;
use App\Service\IteImport\DTO\IteImportRow;
use App\Service\IteImport\Repository\MeasurementUnitProvider;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Convierte un {@see IteImportRow} en una entidad {@see Ite} lista para persistir.
 *
 * Se mantiene aislado del Reader (formato Excel) y del Orquestador (transacciones).
 * Si en el futuro la entidad cambia, este es el único punto que se toca.
 */
final readonly class IteEntityMapper
{
    public function __construct(
        private MeasurementUnitProvider $measurementUnitProvider,
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function toEntity(IteImportRow $row): Ite
    {
        $unit = $this->getMeasurementUnit($row->measurementUnitCode);
        $quality = $this->getQuality($row->quality);
        $iteSource = $this->getIteSource($row->source);
        $city = $this->getCity($row->city, $row->country);
        $iteProjectType = $this->getIteProjectType($row->projectType);

        return (new Ite())
            ->setQuality($quality)
            ->setMeasurementUnit($unit)
            ->setMin($row->min)
            ->setMax($row->max)
            ->setYearReference($row->yearReference)
            ->setComment($row->comment)
            ->setSourceAccess($row->sourceAccess)
            ->setSource($iteSource)
            ->setCity($city)
            ->setProjectType($iteProjectType)
            ->setType(('Cuba' === $row->country) ? IteType::National : IteType::International);
    }

    private function getMeasurementUnit(string $measurementUnit): MeasurementUnit
    {
        return $this->measurementUnitProvider->getByCode($measurementUnit);
    }

    private function getQuality(string $quality): IteQuality
    {
        return IteQuality::getFromLabel($quality);
    }

    private function getIteSource(string $source): IteSource
    {
        $iteSource = $this->entityManager->getRepository(IteSource::class)->findOneBy(['name' => $source]);
        if (null === $iteSource) {
            $iteSource = new IteSource();
            $iteSource->setName($source);

            $this->entityManager->persist($iteSource);
        }

        return $iteSource;
    }

    private function getCity(string $cityName, string $countryName): ?City
    {
        $city = $this->entityManager->getRepository(City::class)->findOneBy(['name' => $cityName]);
        if (null === $city) {
            $city = new City();
            $city->setName($cityName);
        }

        $country = $this->entityManager->getRepository(Country::class)->findOneBy(['name' => $countryName]);
        if (null === $country) {
            $country = new Country();
            $country->setName($countryName);
        }
        $country->addCity($city);
        $this->entityManager->persist($country);
        $this->entityManager->flush();

        return $country->getCity($city->getName());
    }

    private function getIteProjectType(string $projectType): IteProjectType
    {
        $iteProjectType = $this->entityManager->getRepository(IteProjectType::class)->findOneBy(['name' => $projectType]);
        if (null === $iteProjectType) {
            $iteProjectType = new IteProjectType();
            $iteProjectType->setName($projectType);

            $this->entityManager->persist($iteProjectType);
        }

        return $iteProjectType;
    }
}
