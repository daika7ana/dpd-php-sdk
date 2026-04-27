<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Shipments\Responses;

final readonly class CreatedShipmentParcel
{
    public function __construct(
        public int $seqNo,
        public string $id,
        public ?int $externalCarrierId = null,
        public ?string $externalCarrierParcelNumber = null,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            seqNo: (int) ($data['seqNo'] ?? 0),
            id: (string) ($data['id'] ?? ''),
            externalCarrierId: isset($data['externalCarrierId']) ? (int) $data['externalCarrierId'] : null,
            externalCarrierParcelNumber: isset($data['externalCarrierParcelNumber']) ? (string) $data['externalCarrierParcelNumber'] : null,
        );
    }
}
