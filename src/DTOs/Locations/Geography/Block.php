<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Locations\Geography;

final readonly class Block
{
    public function __construct(
        public int $siteId,
        public BilingualText $name,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            siteId: (int) ($data['siteId'] ?? 0),
            name: BilingualText::fromValues(
                isset($data['name']) ? (string) $data['name'] : null,
                isset($data['nameEn']) ? (string) $data['nameEn'] : null,
            ),
        );
    }

    /**
     * @param array<string, string> $row
     */
    public static function fromCsvRow(array $row): self
    {
        return new self(
            siteId: (int) ($row['siteId'] ?? 0),
            name: BilingualText::fromValues($row['name'] ?? null, $row['nameEn'] ?? null),
        );
    }
}
