<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Shipments\Addresses;

interface ShipmentAddressPayload
{
    /**
     * @return array<string, float|int|string>
     */
    public function toArray(): array;
}
