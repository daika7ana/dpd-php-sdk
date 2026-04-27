<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Shipments\Requests;

use Daika7ana\Dpd\DTOs\Common\Addressing\ShipmentParcelRef;

final readonly class BarcodeInformationRequest
{
    public function __construct(
        public ShipmentParcelRef $parcel,
    ) {}

    /**
     * @return array{parcel: array<string, string>}
     */
    public function toArray(): array
    {
        return [
            'parcel' => $this->parcel->toArray(),
        ];
    }
}
