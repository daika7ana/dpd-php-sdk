<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Shipments\Responses;

use Daika7ana\Dpd\DTOs\Common\Support\Collection;
use Daika7ana\Dpd\Enums\Shipments\SecondaryShipmentType;

final readonly class SecondaryShipment
{
    /**
     * @param Collection<int, ShipmentParcelNumber> $parcels
     */
    public function __construct(
        public string $id,
        public SecondaryShipmentType $type,
        public Collection $parcels,
        public ?string $pickupDate = null,
        public ?int $serviceId = null,
        public ?bool $hasScans = null,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $parcels = array_map(
            static fn(array $parcel): ShipmentParcelNumber => ShipmentParcelNumber::fromArray($parcel),
            array_values(array_filter($data['parcels'] ?? [], static fn(mixed $parcel): bool => is_array($parcel))),
        );

        return new self(
            id: (string) ($data['id'] ?? ''),
            type: SecondaryShipmentType::tryFrom((string) ($data['type'] ?? '')) ?? SecondaryShipmentType::ReturnShipment,
            parcels: new Collection($parcels),
            pickupDate: isset($data['pickupDate']) ? (string) $data['pickupDate'] : null,
            serviceId: isset($data['serviceId']) ? (int) $data['serviceId'] : null,
            hasScans: isset($data['hasScans']) ? (bool) $data['hasScans'] : null,
        );
    }
}
