<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Validation\Requests;

use Daika7ana\Dpd\DTOs\Shipments\Addresses\ShipmentAddressPayload;

final readonly class ValidateAddressRequest
{
    public function __construct(
        public ShipmentAddressPayload $address,
    ) {}

    /**
     * @return array{address: array<string, float|int|string>}
     */
    public function toArray(): array
    {
        return ['address' => $this->address->toArray()];
    }
}
