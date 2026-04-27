<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Common\Addressing;

use Daika7ana\Dpd\Support\PayloadFilter;

final readonly class ShipmentParcelRef
{
    public function __construct(
        public ?string $id,
        public ?string $externalCarrierParcelNumber,
        public ?string $fullBarcode,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: isset($data['id']) ? (string) $data['id'] : null,
            externalCarrierParcelNumber: isset($data['externalCarrierParcelNumber']) ? (string) $data['externalCarrierParcelNumber'] : null,
            fullBarcode: isset($data['fullBarcode']) ? (string) $data['fullBarcode'] : null,
        );
    }

    public function hasAnyReference(): bool
    {
        return $this->id !== null || $this->externalCarrierParcelNumber !== null || $this->fullBarcode !== null;
    }

    /**
     * @return array<string, string>
     */
    public function toArray(): array
    {
        return PayloadFilter::removeNulls([
            'id' => $this->id,
            'externalCarrierParcelNumber' => $this->externalCarrierParcelNumber,
            'fullBarcode' => $this->fullBarcode,
        ]);
    }
}
