<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Shipments\Responses;

use Daika7ana\Dpd\DTOs\Common\Support\Collection;

final readonly class CreateShipmentResponse
{
    /**
     * @param Collection<int, CreatedShipmentParcel> $parcels
     */
    public function __construct(
        public string $id,
        public Collection $parcels,
        public ?ShipmentPrice $price = null,
        public ?string $pickupDate = null,
        public ?string $deliveryDeadline = null,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $parcels = array_map(
            static fn(array $parcel): CreatedShipmentParcel => CreatedShipmentParcel::fromArray($parcel),
            array_values(array_filter($data['parcels'] ?? [], static fn(mixed $parcel): bool => is_array($parcel))),
        );

        return new self(
            id: (string) ($data['id'] ?? ''),
            parcels: new Collection($parcels),
            price: isset($data['price']) && is_array($data['price']) ? ShipmentPrice::fromArray($data['price']) : null,
            pickupDate: isset($data['pickupDate']) ? (string) $data['pickupDate'] : null,
            deliveryDeadline: isset($data['deliveryDeadline']) ? (string) $data['deliveryDeadline'] : null,
        );
    }
}
