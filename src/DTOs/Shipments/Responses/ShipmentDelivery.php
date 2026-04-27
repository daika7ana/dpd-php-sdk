<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Shipments\Responses;

final readonly class ShipmentDelivery
{
    public function __construct(
        public ?string $deadline = null,
        public ?string $deliveryDateTime = null,
        public ?string $consignee = null,
        public ?string $deliveryNote = null,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            deadline: isset($data['deadline']) ? (string) $data['deadline'] : null,
            deliveryDateTime: isset($data['deliveryDateTime']) ? (string) $data['deliveryDateTime'] : null,
            consignee: isset($data['consignee']) ? (string) $data['consignee'] : null,
            deliveryNote: isset($data['deliveryNote']) ? (string) $data['deliveryNote'] : null,
        );
    }
}
