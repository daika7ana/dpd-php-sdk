<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Locations\Geography;

final readonly class State
{
    public function __construct(
        public string $id,
        public BilingualText $name,
        public int $countryId,
        public ?string $isoAlpha = null,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: (string) ($data['id'] ?? ''),
            name: BilingualText::fromValues(
                isset($data['name']) ? (string) $data['name'] : null,
                isset($data['nameEn']) ? (string) $data['nameEn'] : null,
            ),
            countryId: (int) ($data['countryId'] ?? 0),
            isoAlpha: isset($data['stateAlpha']) ? (string) $data['stateAlpha'] : (isset($data['isoAlpha']) ? (string) $data['isoAlpha'] : null),
        );
    }

    /**
     * @param array<string, string> $row
     */
    public static function fromCsvRow(array $row): self
    {
        return new self(
            id: (string) ($row['id'] ?? ''),
            name: BilingualText::fromValues($row['name'] ?? null, $row['nameEn'] ?? null),
            countryId: (int) ($row['countryId'] ?? 0),
            isoAlpha: ($row['isoAlpha'] ?? '') !== '' ? $row['isoAlpha'] : null,
        );
    }
}
