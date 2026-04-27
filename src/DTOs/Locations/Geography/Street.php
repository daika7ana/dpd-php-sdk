<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Locations\Geography;

final readonly class Street
{
    public function __construct(
        public int $id,
        public int $siteId,
        public BilingualText $type,
        public BilingualText $name,
        public ?int $actualId,
        public ?BilingualText $actualType,
        public ?BilingualText $actualName,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: (int) ($data['id'] ?? 0),
            siteId: (int) ($data['siteId'] ?? 0),
            type: BilingualText::fromValues(
                isset($data['type']) ? (string) $data['type'] : null,
                isset($data['typeEn']) ? (string) $data['typeEn'] : null,
            ),
            name: BilingualText::fromValues(
                isset($data['name']) ? (string) $data['name'] : null,
                isset($data['nameEn']) ? (string) $data['nameEn'] : null,
            ),
            actualId: isset($data['actualId']) ? (int) $data['actualId'] : null,
            actualType: isset($data['actualType']) || isset($data['actualTypeEn'])
                ? BilingualText::fromValues(
                    isset($data['actualType']) ? (string) $data['actualType'] : null,
                    isset($data['actualTypeEn']) ? (string) $data['actualTypeEn'] : null,
                )
                : null,
            actualName: isset($data['actualName']) || isset($data['actualNameEn'])
                ? BilingualText::fromValues(
                    isset($data['actualName']) ? (string) $data['actualName'] : null,
                    isset($data['actualNameEn']) ? (string) $data['actualNameEn'] : null,
                )
                : null,
        );
    }

    /**
     * @param array<string, string> $row
     */
    public static function fromCsvRow(array $row): self
    {
        $hasActualType = ($row['actualType'] ?? '') !== '' || ($row['actualTypeEn'] ?? '') !== '';
        $hasActualName = ($row['actualName'] ?? '') !== '' || ($row['actualNameEn'] ?? '') !== '';

        return new self(
            id: (int) ($row['id'] ?? 0),
            siteId: (int) ($row['siteId'] ?? 0),
            type: BilingualText::fromValues($row['type'] ?? null, $row['typeEn'] ?? null),
            name: BilingualText::fromValues($row['name'] ?? null, $row['nameEn'] ?? null),
            actualId: ($row['actualId'] ?? '') !== '' ? (int) $row['actualId'] : null,
            actualType: $hasActualType ? BilingualText::fromValues($row['actualType'] ?? null, $row['actualTypeEn'] ?? null) : null,
            actualName: $hasActualName ? BilingualText::fromValues($row['actualName'] ?? null, $row['actualNameEn'] ?? null) : null,
        );
    }
}
