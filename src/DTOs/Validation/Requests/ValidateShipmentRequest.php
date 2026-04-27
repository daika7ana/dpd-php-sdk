<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Validation\Requests;

use Daika7ana\Dpd\DTOs\Shipments\Requests\CreateShipmentRequest;

final readonly class ValidateShipmentRequest
{
    public function __construct(
        public CreateShipmentRequest $shipment,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return $this->shipment->toArray();
    }
}
