<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Tracking\Models;

use Daika7ana\Dpd\DTOs\Common\Addressing\ShipmentParcelRef;
use Daika7ana\Dpd\Support\PayloadFilter;

final readonly class TrackShipmentParcelRef
{
    public function __construct(
        public ?string $ref = null,
        public ?string $id = null,
        public ?string $externalCarrierParcelNumber = null,
        public ?string $fullBarcode = null,
    ) {}

    public static function fromParcelRef(ShipmentParcelRef $parcelRef): self
    {
        return new self(
            id: $parcelRef->id,
            externalCarrierParcelNumber: $parcelRef->externalCarrierParcelNumber,
            fullBarcode: $parcelRef->fullBarcode,
        );
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            ref: isset($data['ref']) ? (string) $data['ref'] : null,
            id: isset($data['id']) ? (string) $data['id'] : null,
            externalCarrierParcelNumber: isset($data['externalCarrierParcelNumber']) ? (string) $data['externalCarrierParcelNumber'] : null,
            fullBarcode: isset($data['fullBarcode']) ? (string) $data['fullBarcode'] : null,
        );
    }

    /**
     * @return array<string, string>
     */
    public function toArray(): array
    {
        return PayloadFilter::removeNulls([
            'ref' => $this->ref,
            'id' => $this->id,
            'externalCarrierParcelNumber' => $this->externalCarrierParcelNumber,
            'fullBarcode' => $this->fullBarcode,
        ]);
    }
}
