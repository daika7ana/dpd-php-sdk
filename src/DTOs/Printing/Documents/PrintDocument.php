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
            format: self::detectFormat($binaryContent),
        );
    }

    public function isPdf(): bool
    {
        return $this->format === PrintFormat::Pdf;
    }

    public function isZpl(): bool
    {
        return $this->format === PrintFormat::Zpl;
    }

    private static function detectFormat(BinaryContent $binaryContent): ?PrintFormat
    {
        return match (true) {
            $binaryContent->mimeType === 'application/pdf',
            $binaryContent->mimeType === 'application/octet-stream' && str_starts_with($binaryContent->contents, '%PDF-') => PrintFormat::Pdf,
            $binaryContent->mimeType === 'text/plain',
            $binaryContent->mimeType === 'application/octet-stream' => PrintFormat::Zpl,
            default => null,
        };
    }
}
