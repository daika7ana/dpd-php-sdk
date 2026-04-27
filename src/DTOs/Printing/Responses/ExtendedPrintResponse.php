<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Printing\Responses;

use Daika7ana\Dpd\DTOs\Common\Support\Collection;
use Daika7ana\Dpd\DTOs\Common\Documents\LabelInfo;
use Daika7ana\Dpd\DTOs\Printing\Documents\PrintDocument;
use Daika7ana\Dpd\Enums\Printing\PrintFormat;

final readonly class ExtendedPrintResponse
{
    /**
     * @param Collection<int, LabelInfo> $printLabelsInfo
     */
    public function __construct(
        public string $data,
        public Collection $printLabelsInfo,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $labels = array_map(
            static fn(array $labelInfo): LabelInfo => LabelInfo::fromArray($labelInfo),
            array_values(array_filter($data['printLabelsInfo'] ?? [], static fn(mixed $labelInfo): bool => is_array($labelInfo))),
        );

        return new self(
            data: (string) ($data['data'] ?? ''),
            printLabelsInfo: new Collection($labels),
        );
    }

    public function asDocument(): PrintDocument
    {
        $decoded = base64_decode($this->data, true);

        return new PrintDocument(
            contents: $decoded === false ? '' : $decoded,
            mimeType: 'application/pdf',
            format: PrintFormat::Pdf,
        );
    }
}
