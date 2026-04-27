<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Locations\Geography;

use Daika7ana\Dpd\Enums\Locations\AddressType;
use Daika7ana\Dpd\Enums\Locations\NomenclatureSupport;
use Daika7ana\Dpd\Support\CsvHelper;

final readonly class Country
{
    /**
     * @param list<string> $postCodeFormats
     * @param list<AddressNomenclatureType> $streetTypes
     * @param list<AddressNomenclatureType> $complexTypes
     */
    public function __construct(
        public int $id,
        public BilingualText $name,
        public string $isoAlpha2,
        public string $isoAlpha3,
        public array $postCodeFormats,
        public bool $requireState,
        public AddressType $addressType,
        public ?string $currencyCode,
        public ?int $defaultOfficeId,
        public array $streetTypes,
        public array $complexTypes,
        public NomenclatureSupport $siteNomenclature,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $streetTypes = [];

        foreach ($data['streetTypes'] ?? [] as $rawStreetType) {
            if (is_array($rawStreetType)) {
                $streetTypes[] = AddressNomenclatureType::fromArray($rawStreetType);
            }
        }

        $complexTypes = [];

        foreach ($data['complexTypes'] ?? [] as $rawComplexType) {
            if (is_array($rawComplexType)) {
                $complexTypes[] = AddressNomenclatureType::fromArray($rawComplexType);
            }
        }

        $postCodeFormats = [];

        foreach ($data['postCodeFormats'] ?? [] as $postCodeFormat) {
            if (is_scalar($postCodeFormat)) {
                $postCodeFormats[] = (string) $postCodeFormat;
            }
        }

        return new self(
            id: (int) ($data['id'] ?? 0),
            name: BilingualText::fromValues(
                isset($data['name']) ? (string) $data['name'] : null,
                isset($data['nameEn']) ? (string) $data['nameEn'] : null,
            ),
            isoAlpha2: (string) ($data['isoAlpha2'] ?? ''),
            isoAlpha3: (string) ($data['isoAlpha3'] ?? ''),
            postCodeFormats: $postCodeFormats,
            requireState: (bool) ($data['requireState'] ?? false),
            addressType: AddressType::from((int) ($data['addressType'] ?? AddressType::Local->value)),
            currencyCode: isset($data['currencyCode']) ? (string) $data['currencyCode'] : null,
            defaultOfficeId: isset($data['defaultOfficeId']) ? (int) $data['defaultOfficeId'] : null,
            streetTypes: $streetTypes,
            complexTypes: $complexTypes,
            siteNomenclature: NomenclatureSupport::from((int) ($data['siteNomen'] ?? NomenclatureSupport::None->value)),
        );
    }

    /**
     * @param array<string, string> $row
     */
    public static function fromCsvRow(array $row): self
    {
        $streetTypes = self::pairNomenclatureTypes($row['streetTypes'] ?? '', $row['streetTypesEn'] ?? '');
        $complexTypes = self::pairNomenclatureTypes($row['complexTypes'] ?? '', $row['complexTypesEn'] ?? '');

        return new self(
            id: (int) ($row['id'] ?? 0),
            name: BilingualText::fromValues($row['name'] ?? null, $row['nameEn'] ?? null),
            isoAlpha2: (string) ($row['isoAlpha2'] ?? ''),
            isoAlpha3: (string) ($row['isoAlpha3'] ?? ''),
            postCodeFormats: CsvHelper::splitList($row['postCodeFormats'] ?? null, true),
            requireState: CsvHelper::toBool($row['requireState'] ?? null),
            addressType: AddressType::from((int) ($row['addressType'] ?? AddressType::Local->value)),
            currencyCode: ($row['currencyCode'] ?? '') !== '' ? $row['currencyCode'] : null,
            defaultOfficeId: ($row['defaultOfficeId'] ?? '') !== '' ? (int) $row['defaultOfficeId'] : null,
            streetTypes: $streetTypes,
            complexTypes: $complexTypes,
            siteNomenclature: NomenclatureSupport::from((int) ($row['siteNomen'] ?? NomenclatureSupport::None->value)),
        );
    }

    /**
     * @return list<AddressNomenclatureType>
     */
    private static function pairNomenclatureTypes(string $local, string $english): array
    {
        $localItems = CsvHelper::splitList($local);
        $englishItems = CsvHelper::splitList($english);
        $types = [];
        $count = max(count($localItems), count($englishItems));

        for ($index = 0; $index < $count; $index++) {
            $types[] = AddressNomenclatureType::fromCsvValues(
                $localItems[$index] ?? '',
                $englishItems[$index] ?? '',
            );
        }

        return $types;
    }
}
