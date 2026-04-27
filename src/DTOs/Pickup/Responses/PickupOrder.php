<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Pickup\Responses;

use Daika7ana\Dpd\DTOs\Common\Support\Collection;

final readonly class PickupOrder
{
    /**
     * @param Collection<int, string> $shipmentIds
     */
    public function __construct(
        public int $id,
        public Collection $shipmentIds,
        public ?string $pickupPeriodFrom = null,
        public ?string $pickupPeriodTo = null,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $shipmentIds = array_map(
            static fn(mixed $shipmentId): string => (string) $shipmentId,
            array_values(array_filter($data['shipmentIds'] ?? [], static fn(mixed $shipmentId): bool => is_scalar($shipmentId))),
        );

        return new self(
            id: (int) ($data['id'] ?? 0),
            shipmentIds: new Collection($shipmentIds),
            pickupPeriodFrom: isset($data['pickupPeriodFrom']) ? (string) $data['pickupPeriodFrom'] : null,
            pickupPeriodTo: isset($data['pickupPeriodTo']) ? (string) $data['pickupPeriodTo'] : null,
        );
    }
}
