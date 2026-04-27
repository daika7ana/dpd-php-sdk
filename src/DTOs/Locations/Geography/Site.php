<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Locations\Geography;

use Daika7ana\Dpd\Enums\Locations\NomenclatureSupport;

final readonly class Site
{
    public function __construct(
        public int $id,
        public int $countryId,
        public ?int $mainSiteId,
        public BilingualText $type,
        public BilingualText $name,
        public BilingualText $municipality,
        public BilingualText $region,
        public ?string $postCode,
        public NomenclatureSupport $addressNomenclature,
        public ?float $x,
        public ?float $y,
        public ?string $servingDays,
        public ?int $servingOfficeId,
        public ?int $servingHubOfficeId,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: (int) ($data['id'] ?? 0),
            countryId: (int) ($data['countryId'] ?? 0),
            mainSiteId: isset($data['mainSiteId']) ? (int) $data['mainSiteId'] : null,
            type: BilingualText::fromValues(
                isset($data['type']) ? (string) $data['type'] : null,
                isset($data['typeEn']) ? (string) $data['typeEn'] : null,
            ),
            name: BilingualText::fromValues(
                isset($data['name']) ? (string) $data['name'] : null,
                isset($data['nameEn']) ? (string) $data['nameEn'] : null,
            ),
            municipality: BilingualText::fromValues(
                isset($data['municipality']) ? (string) $data['municipality'] : null,
                isset($data['municipalityEn']) ? (string) $data['municipalityEn'] : null,
            ),
            region: BilingualText::fromValues(
                isset($data['region']) ? (string) $data['region'] : null,
                isset($data['regionEn']) ? (string) $data['regionEn'] : null,
            ),
            postCode: isset($data['postCode']) ? (string) $data['postCode'] : null,
            addressNomenclature: NomenclatureSupport::from((int) ($data['addressNomenclature'] ?? NomenclatureSupport::None->value)),
            x: isset($data['x']) ? (float) $data['x'] : null,
            y: isset($data['y']) ? (float) $data['y'] : null,
            servingDays: isset($data['servingDays']) ? (string) $data['servingDays'] : null,
            servingOfficeId: isset($data['servingOfficeId']) ? (int) $data['servingOfficeId'] : null,
            servingHubOfficeId: isset($data['servingHubOfficeId']) ? (int) $data['servingHubOfficeId'] : null,
        );
    }

    /**
     * @param array<string, string> $row
     */
    public static function fromCsvRow(array $row): self
    {
        return new self(
            id: (int) ($row['id'] ?? 0),
            countryId: (int) ($row['countryId'] ?? 0),
            mainSiteId: ($row['mainSiteId'] ?? '') !== '' ? (int) $row['mainSiteId'] : null,
            type: BilingualText::fromValues($row['type'] ?? null, $row['typeEn'] ?? null),
            name: BilingualText::fromValues($row['name'] ?? null, $row['nameEn'] ?? null),
            municipality: BilingualText::fromValues($row['municipality'] ?? null, $row['municipalityEn'] ?? null),
            region: BilingualText::fromValues($row['region'] ?? null, $row['regionEn'] ?? null),
            postCode: ($row['postCode'] ?? '') !== '' ? $row['postCode'] : null,
            addressNomenclature: NomenclatureSupport::from((int) ($row['addressNomenclature'] ?? NomenclatureSupport::None->value)),
            x: ($row['x'] ?? '') !== '' ? (float) $row['x'] : null,
            y: ($row['y'] ?? '') !== '' ? (float) $row['y'] : null,
            servingDays: ($row['servingDays'] ?? '') !== '' ? $row['servingDays'] : null,
            servingOfficeId: ($row['servingOfficeId'] ?? '') !== '' ? (int) $row['servingOfficeId'] : null,
            servingHubOfficeId: ($row['servingHubOfficeId'] ?? '') !== '' ? (int) $row['servingHubOfficeId'] : null,
        );
    }
}
