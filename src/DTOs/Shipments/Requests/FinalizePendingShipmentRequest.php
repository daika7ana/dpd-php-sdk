<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Shipments\Requests;

final readonly class FinalizePendingShipmentRequest
{
    public function __construct(
        public string $shipmentId,
    ) {}

    /**
     * @return array{shipmentId: string}
     */
    public function toArray(): array
    {
        return ['shipmentId' => $this->shipmentId];
    }
}
