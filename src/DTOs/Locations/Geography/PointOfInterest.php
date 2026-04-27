<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Locations\Geography;

final readonly class PointOfInterest
{
    public function __construct(
        public int $id,
        public int $siteId,
        public BilingualText $name,
        public BilingualText $type,
        public BilingualText $address,
        public ?float $x,
        public ?float $y,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: (int) ($data['id'] ?? 0),
            siteId: (int) ($data['siteId'] ?? 0),
            name: BilingualText::fromValues(
                isset($data['name']) ? (string) $data['name'] : null,
                isset($data['nameEn']) ? (string) $data['nameEn'] : null,
            ),
            type: BilingualText::fromValues(
                isset($data['type']) ? (string) $data['type'] : null,
                isset($data['typeEn']) ? (string) $data['typeEn'] : null,
            ),
            address: BilingualText::fromValues(
                isset($data['address']) ? (string) $data['address'] : null,
                isset($data['addressEn']) ? (string) $data['addressEn'] : null,
            ),
            x: isset($data['x']) ? (float) $data['x'] : null,
            y: isset($data['y']) ? (float) $data['y'] : null,
        );
    }

    /**
     * @param array<string, string> $row
     */
    public static function fromCsvRow(array $row): self
    {
        return new self(
            id: (int) ($row['id'] ?? 0),
            siteId: (int) ($row['siteId'] ?? 0),
            name: BilingualText::fromValues($row['name'] ?? null, $row['nameEn'] ?? null),
            type: BilingualText::fromValues($row['type'] ?? null, $row['typeEn'] ?? null),
            address: BilingualText::fromValues($row['address'] ?? null, $row['addressEn'] ?? null),
            x: ($row['x'] ?? '') !== '' ? (float) $row['x'] : null,
            y: ($row['y'] ?? '') !== '' ? (float) $row['y'] : null,
        );
    }
}
