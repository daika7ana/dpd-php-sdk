<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Shipments\Responses;

use Daika7ana\Dpd\DTOs\Common\Support\Collection;

final readonly class FindParcelsByReferenceResponse
{
    /**
     * @param Collection<int, string> $barcodes
     */
    public function __construct(
        public Collection $barcodes,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        /** @var list<string> $barcodes */
        $barcodes = array_values(array_map('strval', $data['barcodes'] ?? []));

        return new self(new Collection($barcodes));
    }
}
