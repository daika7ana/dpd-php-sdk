<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Shipments\Requests;

use Daika7ana\Dpd\DTOs\Shipments\Payloads\ShipmentContent;
use Daika7ana\Dpd\DTOs\Shipments\Payloads\ShipmentPayment;
use Daika7ana\Dpd\DTOs\Shipments\Parties\ShipmentRecipient;
use Daika7ana\Dpd\DTOs\Shipments\Parties\ShipmentSender;
use Daika7ana\Dpd\DTOs\Shipments\Payloads\ShipmentService;
use Daika7ana\Dpd\Support\PayloadFilter;

final readonly class CreateShipmentRequest
{
    public function __construct(
        public ShipmentRecipient $recipient,
        public ShipmentService $service,
        public ShipmentContent $content,
        public ShipmentPayment $payment,
        public ?ShipmentSender $sender = null,
        public ?string $id = null,
        public ?string $shipmentNote = null,
        public ?string $ref1 = null,
        public ?string $ref2 = null,
        public ?string $consolidationRef = null,
        public ?bool $requireUnsuccessfulDeliveryStickerImage = null,
    ) {}

    /**
     * @return array<string, array<string, mixed>|bool|string>
     */
    public function toArray(): array
    {
        return PayloadFilter::removeNulls([
            'id' => $this->id,
            'sender' => $this->sender?->toArray(),
            'recipient' => $this->recipient->toArray(),
            'service' => $this->service->toArray(),
            'content' => $this->content->toArray(),
            'payment' => $this->payment->toArray(),
            'shipmentNote' => $this->shipmentNote,
            'ref1' => $this->ref1,
            'ref2' => $this->ref2,
            'consolidationRef' => $this->consolidationRef,
            'requireUnsuccessfulDeliveryStickerImage' => $this->requireUnsuccessfulDeliveryStickerImage,
        ]);
    }
}
