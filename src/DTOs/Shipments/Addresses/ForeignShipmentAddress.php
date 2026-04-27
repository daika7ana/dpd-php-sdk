<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Shipments\Addresses;

use Daika7ana\Dpd\DTOs\Common\Addressing\AddressLocation;
use Daika7ana\Dpd\Support\PayloadFilter;

final readonly class ForeignShipmentAddress implements ShipmentAddressPayload
{
    public function __construct(
        public AddressLocation $location,
        public string $addressLine1,
        public ?string $addressLine2 = null,
    ) {}

    public function toArray(): array
    {
        return PayloadFilter::removeNulls([
            ...$this->location->toArray(),
            'addressLine1' => $this->addressLine1,
            'addressLine2' => $this->addressLine2,
        ]);
    }
}
