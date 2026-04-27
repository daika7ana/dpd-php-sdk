<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Shipments\Responses;

use Daika7ana\Dpd\DTOs\Common\Support\Collection;

final readonly class SecondaryShipmentsResponse
{
    /**
     * @param Collection<int, SecondaryShipment> $shipments
     */
    public function __construct(
        public Collection $shipments,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $shipments = array_map(
            static fn(array $shipment): SecondaryShipment => SecondaryShipment::fromArray($shipment),
            array_values(array_filter($data['shipments'] ?? [], static fn(mixed $shipment): bool => is_array($shipment))),
        );

        return new self(new Collection($shipments));
    }
}
