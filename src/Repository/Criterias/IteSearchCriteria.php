<?php

namespace App\Repository\Criterias;

use App\Entity\Enums\IteType;

final readonly class IteSearchCriteria
{
    public function __construct(
        public string $filter = '',
        public ?IteType $type = null,
        public string $quality = '',
        public string $measurementUnit = '',
        public string $source = '',
        public string $projectType = '',
        public string $city = '',
        public string $country = '',
    ) {
    }
}
