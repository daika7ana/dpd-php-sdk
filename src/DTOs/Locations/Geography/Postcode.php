<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Locations\Geography;

final readonly class Postcode
{
    public function __construct(
        public string $postCode,
        public int $siteId,
    ) {}

    /**
     * @param array<string, string> $row
     */
    public static function fromCsvRow(array $row): self
    {
        return new self(
            postCode: (string) ($row['postcode'] ?? $row['postCode'] ?? ''),
            siteId: (int) ($row['siteId'] ?? 0),
        );
    }
}
