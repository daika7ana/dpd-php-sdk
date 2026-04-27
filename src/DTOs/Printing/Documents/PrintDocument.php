<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Printing\Documents;

use Daika7ana\Dpd\DTOs\Common\Documents\BinaryContent;
use Daika7ana\Dpd\Enums\Printing\PrintFormat;

final readonly class PrintDocument
{
    public function __construct(
        public string $contents,
        public string $mimeType,
        public ?PrintFormat $format = null,
    ) {}

    public static function fromBinary(BinaryContent $binaryContent): self
    {
        return new self(
            contents: $binaryContent->contents,
            mimeType: $binaryContent->mimeType,
            format: match ($binaryContent->mimeType) {
                'application/pdf' => PrintFormat::Pdf,
                'text/plain' => PrintFormat::Zpl,
                default => null,
            },
        );
    }

    public function isPdf(): bool
    {
        return $this->mimeType === 'application/pdf';
    }

    public function isZpl(): bool
    {
        return $this->mimeType === 'text/plain';
    }
}
