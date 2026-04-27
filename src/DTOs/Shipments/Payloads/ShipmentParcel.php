<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Shipments\Payloads;

use Daika7ana\Dpd\DTOs\Common\Addressing\ShipmentParcelSize;
use Daika7ana\Dpd\Support\PayloadFilter;

final readonly class ShipmentParcel
{
    /**
     * @param list<GoodsItem> $goods
     */
    public function __construct(
        public int $seqNo,
        public float $weight,
        public ?string $id = null,
        public ?int $packageUniqueNumber = null,
        public ?ShipmentParcelSize $size = null,
        public ?string $ref1 = null,
        public ?string $ref2 = null,
        public ?ExternalCarrierParcelNumber $pickupExternalCarrierParcelNumber = null,
        public ?ExternalCarrierParcelNumber $deliveryExternalCarrierParcelNumber = null,
        public array $goods = [],
        public ?ParcelToPrintAdditionalBarcode $printAdditionalBarcode = null,
    ) {}

    /**
     * @return array<string, array<string, mixed>|array<int, array<string, float|int|string>>|float|int|string>
     */
    public function toArray(): array
    {
        return PayloadFilter::removeNulls([
            'id' => $this->id,
            'seqNo' => $this->seqNo,
            'packageUniqueNumber' => $this->packageUniqueNumber,
            'size' => $this->size?->toArray(),
            'weight' => $this->weight,
            'ref1' => $this->ref1,
            'ref2' => $this->ref2,
            'pickupExternalCarrierParcelNumber' => $this->pickupExternalCarrierParcelNumber?->toArray(),
            'deliveryExternalCarrierParcelNumber' => $this->deliveryExternalCarrierParcelNumber?->toArray(),
            'goods' => array_map(static fn(GoodsItem $item): array => $item->toArray(), $this->goods),
            'printAdditionalBarcode' => $this->printAdditionalBarcode?->toArray(),
        ]);
    }
}
