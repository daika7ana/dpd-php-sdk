<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Shipments\Requests;

final readonly class CancelShipmentRequest
{
    public function __construct(
        public string $shipmentId,
        public string $comment,
    ) {}

    /**
     * @return array{shipmentId: string, comment: string}
     */
    public function toArray(): array
    {
        return [
            'shipmentId' => $this->shipmentId,
            'comment' => $this->comment,
        ];
    }
}
