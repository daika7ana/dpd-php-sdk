<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Shipments\Payloads;

use Daika7ana\Dpd\Support\PayloadFilter;

final readonly class ShipmentContent
{
    /**
     * @param list<ShipmentParcel> $parcels
     */
    public function __construct(
        public string $contents,
        public string $package,
        public ?int $parcelsCount = null,
        public ?float $totalWeight = null,
        public ?bool $documents = null,
        public ?bool $palletized = null,
        public array $parcels = [],
        public ?bool $pendingParcels = null,
        public ?bool $exciseGoods = null,
        public ?bool $lq = null,
        public ?float $goodsValue = null,
        public ?string $goodsValueCurrencyCode = null,
        public ?string $uitCode = null,
        public ?ParcelToPrintAdditionalBarcode $printAdditionalBarcode = null,
    ) {}

    /**
     * @return array<string, array<int, array<string, mixed>>|array<string, string>|bool|float|int|string>
     */
    public function toArray(): array
    {
        return PayloadFilter::removeNulls([
            'parcelsCount' => $this->parcelsCount,
            'totalWeight' => $this->totalWeight,
            'contents' => $this->contents,
            'package' => $this->package,
            'documents' => $this->documents,
            'palletized' => $this->palletized,
            'parcels' => array_map(static fn(ShipmentParcel $parcel): array => $parcel->toArray(), $this->parcels),
            'pendingParcels' => $this->pendingParcels,
            'exciseGoods' => $this->exciseGoods,
            'lq' => $this->lq,
            'goodsValue' => $this->goodsValue,
            'goodsValueCurrencyCode' => $this->goodsValueCurrencyCode,
            'uitCode' => $this->uitCode,
            'printAdditionalBarcode' => $this->printAdditionalBarcode?->toArray(),
        ]);
    }
}
