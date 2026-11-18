<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Locations\Requests;

use Daika7ana\Dpd\Enums\Locations\OfficeFeature;
use Daika7ana\Dpd\Enums\Locations\OfficeType;
use Daika7ana\Dpd\Support\PayloadFilter;

final readonly class OfficeSearchRequest
{
    /**
     * @param list<OfficeType>|null $officeType
     * @param list<OfficeFeature>|null $officeFeatures
     */
    public function __construct(
        public ?int $countryId = null,
        public ?int $siteId = null,
        public ?string $siteName = null,
        public ?string $name = null,
        public ?int $limit = null,
        public ?array $officeType = null,
        public ?array $officeFeatures = null,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return PayloadFilter::removeNulls([
            'countryId' => $this->countryId,
            'siteId' => $this->siteId,
            'siteName' => $this->siteName,
            'name' => $this->name,
            'limit' => $this->limit,
            'officeType' => $this->officeType !== null
                ? array_map(static fn(OfficeType $type): string => $type->value, $this->officeType)
                : null,
            'officeFeatures' => $this->officeFeatures !== null
                ? array_map(static fn(OfficeFeature $feature): string => $feature->value, $this->officeFeatures)
                : null,
        ]);
    }
}
