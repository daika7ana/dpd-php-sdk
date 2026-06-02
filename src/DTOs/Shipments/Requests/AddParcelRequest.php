<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Shipments\Requests;

use Daika7ana\Dpd\DTOs\Shipments\AdditionalServices\ShipmentCodFiscalReceiptItem;
use Daika7ana\Dpd\DTOs\Shipments\Payloads\ShipmentParcel;
use Daika7ana\Dpd\Support\PayloadFilter;

final readonly class AddParcelRequest
{
    /**
     * @param list<ShipmentCodFiscalReceiptItem> $codFiscalReceiptItems
     */
    public function __construct(
        public string $shipmentId,
        public ShipmentParcel $parcel,
        public array $codFiscalReceiptItems = [],
        public ?float $codAmount = null,
        public ?float $declaredValueAmount = null,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return PayloadFilter::removeNulls([
            'shipmentId' => $this->shipmentId,
            'parcel' => $this->parcel->toArray(),
            'codAmount' => $this->codAmount,
            'codFiscalReceiptItems' => array_map(
                static fn(ShipmentCodFiscalReceiptItem $item): array => $item->toArray(),
                $this->codFiscalReceiptItems,
            ),
            'declaredValueAmount' => $this->declaredValueAmount,
        ]);
    }
}
