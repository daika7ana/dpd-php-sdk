<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Calculations\Payloads;

use Daika7ana\Dpd\DTOs\Shipments\Payloads\ShipmentParcel;
use Daika7ana\Dpd\Support\PayloadFilter;
use InvalidArgumentException;

final readonly class CalculationContent
{
    /**
     * @param list<ShipmentParcel> $parcels
     */
    public function __construct(
        public array $parcels = [],
        public ?int $parcelsCount = null,
        public ?float $totalWeight = null,
        public ?bool $documents = null,
        public ?bool $palletized = null,
    ) {}

    /**
     * @return array<string, bool|float|int|list<array<string, mixed>>>
     */
    public function toArray(): array
    {
        if ($this->parcels === [] && ($this->parcelsCount === null || $this->totalWeight === null)) {
            throw new InvalidArgumentException('CalculationContent needs at least one parcel, or both parcelsCount and totalWeight.');
        }

        return PayloadFilter::removeNullsAndEmptyArrays([
            'parcelsCount' => $this->parcelsCount,
            'totalWeight' => $this->totalWeight,
            'documents' => $this->documents,
            'palletized' => $this->palletized,
            'parcels' => array_map(
                static fn(ShipmentParcel $parcel): array => $parcel->toArray(),
                $this->parcels,
            ),
        ]);
    }
}
