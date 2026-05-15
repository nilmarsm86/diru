<?php

declare(strict_types=1);

namespace App\Service\IteImport\Result;

final readonly class ImportError
{
    public function __construct(
        public string $sheet,
        public int $rowNumber,
        public string $message,
    ) {
    }

    public function __toString(): string
    {
        return sprintf('[%s:%d] %s', $this->sheet, $this->rowNumber, $this->message);
    }
}
