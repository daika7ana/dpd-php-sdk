<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Shipments\Requests;

final readonly class UpdateShipmentRequest
{
    public function __construct(
        public string $id,
        public CreateShipmentRequest $shipment,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            ...$this->shipment->toArray(),
            'id' => $this->id,
        ];
    }
}
