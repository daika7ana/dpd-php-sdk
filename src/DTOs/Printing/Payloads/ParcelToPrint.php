<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Printing\Payloads;

use Daika7ana\Dpd\DTOs\Common\Addressing\ShipmentParcelRef;
use Daika7ana\Dpd\DTOs\Shipments\Payloads\ParcelToPrintAdditionalBarcode;
use Daika7ana\Dpd\Support\PayloadFilter;

final readonly class ParcelToPrint
{
    public function __construct(
        public ShipmentParcelRef $parcel,
        public ?ParcelToPrintAdditionalBarcode $additionalBarcode = null,
    ) {}

    /**
     * @return array<string, array<string, mixed>>
     */
    public function toArray(): array
    {
        return PayloadFilter::removeNulls([
            'parcel' => $this->parcel->toArray(),
            'additionalBarcode' => $this->additionalBarcode?->toArray(),
        ]);
    }
}
