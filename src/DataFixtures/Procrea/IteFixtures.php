<?php

namespace App\DataFixtures\Procrea;

use App\Service\IteImport\IteImportService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class IteFixtures extends Fixture implements FixtureGroupInterface
{
    public function __construct(
        private readonly IteImportService $iteImportService,
        #[Autowire('%kernel.project_dir%/var/ite/excel')] private readonly string $iteExcelDirectory,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $this->iteImportService->import($this->iteExcelDirectory.'/BASE DE DATOS DE FUENTES INTERNACIONALES 2024-2025.xlsx');
        $this->iteImportService->import($this->iteExcelDirectory.'/ITE_CENCREM_2017 ACTUALIZACION 2025.xlsx');
    }

    public static function getGroups(): array
    {
        return ['procrea'];
    }

    public function getOrder(): int
    {
        return 14;
    }
}
