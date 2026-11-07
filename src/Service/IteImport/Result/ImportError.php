<?php

declare(strict_types=1);

namespace App\Service\IteImport\Result;

final readonly class ImportError
{
    public function __construct(
        public ?string $sheet = null,
        public ?int $rowNumber = null,
        public string $message = 'Ha ocurrido un error.',
    ) {
    }

    public function __toString(): string
    {
        if (null !== $this->rowNumber || null !== $this->sheet) {
            return sprintf('[%s:%d] %s', $this->sheet, $this->rowNumber, $this->message);
        }

        return sprintf('%s', $this->message);
    }
}
