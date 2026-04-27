<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Shipments\Responses;

use Daika7ana\Dpd\DTOs\Shipments\Payloads\ShipmentService;

final readonly class ShipmentRecord
{
    public function __construct(
        public string $id,
        public ?ShipmentParty $sender = null,
        public ?ShipmentParty $recipient = null,
        public ?ShipmentService $service = null,
        public ?ShipmentContentRecord $content = null,
        public ?ShipmentPaymentRecord $payment = null,
        public ?string $shipmentNote = null,
        public ?string $ref1 = null,
        public ?string $ref2 = null,
        public ?ShipmentPrice $price = null,
        public ?ShipmentDelivery $delivery = null,
        public ?PrimaryShipment $primaryShipment = null,
        public ?string $returnShipmentId = null,
        public ?string $redirectShipmentId = null,
        public ?bool $pendingShipment = null,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: (string) ($data['id'] ?? ''),
            sender: isset($data['sender']) && is_array($data['sender']) ? ShipmentParty::fromArray($data['sender']) : null,
            recipient: isset($data['recipient']) && is_array($data['recipient']) ? ShipmentParty::fromArray($data['recipient']) : null,
            service: isset($data['service']) && is_array($data['service']) ? ShipmentService::fromArray($data['service']) : null,
            content: isset($data['content']) && is_array($data['content']) ? ShipmentContentRecord::fromArray($data['content']) : null,
            payment: isset($data['payment']) && is_array($data['payment']) ? ShipmentPaymentRecord::fromArray($data['payment']) : null,
            shipmentNote: isset($data['shipmentNote']) ? (string) $data['shipmentNote'] : null,
            ref1: isset($data['ref1']) ? (string) $data['ref1'] : null,
            ref2: isset($data['ref2']) ? (string) $data['ref2'] : null,
            price: isset($data['price']) && is_array($data['price']) ? ShipmentPrice::fromArray($data['price']) : null,
            delivery: isset($data['delivery']) && is_array($data['delivery']) ? ShipmentDelivery::fromArray($data['delivery']) : null,
            primaryShipment: isset($data['primaryShipment']) && is_array($data['primaryShipment']) ? PrimaryShipment::fromArray($data['primaryShipment']) : null,
            returnShipmentId: isset($data['returnShipmentId']) ? (string) $data['returnShipmentId'] : null,
            redirectShipmentId: isset($data['redirectShipmentId']) ? (string) $data['redirectShipmentId'] : null,
            pendingShipment: isset($data['pendingShipment']) ? (bool) $data['pendingShipment'] : null,
        );
    }
}
