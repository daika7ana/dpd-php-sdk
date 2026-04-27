<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Shipments\Requests;

final readonly class ShipmentInformationRequest
{
    /**
     * @param list<string> $shipmentIds
     */
    public function __construct(
        public array $shipmentIds,
    ) {}

    /**
     * @return array{shipmentIds: list<string>}
     */
    public function toArray(): array
    {
        return ['shipmentIds' => $this->shipmentIds];
    }
}
