<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Locations\Requests;

use Daika7ana\Dpd\DTOs\Shipments\Addresses\ShipmentAddressPayload;
use Daika7ana\Dpd\Enums\Locations\OfficeFeature;
use Daika7ana\Dpd\Enums\Locations\OfficeType;
use Daika7ana\Dpd\Support\PayloadFilter;

final readonly class NearestOfficesRequest
{
    /**
     * @param list<OfficeFeature>|null $officeFeatures
     */
    public function __construct(
        public ShipmentAddressPayload $address,
        public ?int $distance = null,
        public ?int $limit = null,
        public ?OfficeType $officeType = null,
        public ?array $officeFeatures = null,
    ) {}

    /**
     * @return array<string, array<int, string>|array<string, float|int|string>|int|string>
     */
    public function toArray(): array
    {
        return PayloadFilter::removeNulls([
            'address' => $this->address->toArray(),
            'distance' => $this->distance,
            'limit' => $this->limit,
            'officeType' => $this->officeType?->value,
            'officeFeatures' => $this->officeFeatures !== null
                ? array_map(static fn(OfficeFeature $feature): string => $feature->value, $this->officeFeatures)
                : null,
        ]);
    }
}
