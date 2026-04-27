<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Payments\Payouts;

final readonly class PayoutDetail
{
    public function __construct(
        public int $lineNo,
        public string $shipmentId,
        public ?string $pickupDate = null,
        public ?string $primaryShipmentPickupDate = null,
        public ?string $deliveryDate = null,
        public ?string $sender = null,
        public ?string $recipient = null,
        public ?string $note = null,
        public ?string $ref1 = null,
        public ?string $ref2 = null,
        public ?string $currency = null,
        public ?int $order = null,
        public ?float $amount = null,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            lineNo: (int) ($data['lineNo'] ?? 0),
            shipmentId: (string) ($data['shipmentId'] ?? ''),
            pickupDate: isset($data['pickupDate']) ? (string) $data['pickupDate'] : null,
            primaryShipmentPickupDate: isset($data['primaryShipmentPickupDate']) ? (string) $data['primaryShipmentPickupDate'] : null,
            deliveryDate: isset($data['deliveryDate']) ? (string) $data['deliveryDate'] : null,
            sender: isset($data['sender']) ? (string) $data['sender'] : null,
            recipient: isset($data['recipient']) ? (string) $data['recipient'] : null,
            note: isset($data['note']) ? (string) $data['note'] : null,
            ref1: isset($data['ref1']) ? (string) $data['ref1'] : null,
            ref2: isset($data['ref2']) ? (string) $data['ref2'] : null,
            currency: isset($data['currency']) ? (string) $data['currency'] : null,
            order: isset($data['order']) ? (int) $data['order'] : null,
            amount: isset($data['amount']) ? (float) $data['amount'] : null,
        );
    }
}
