<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Shipments\AdditionalServices;

final readonly class ShipmentRopAdditionalServiceLine
{
    public function __construct(
        public int $serviceId,
        public int $parcelsCount,
    ) {}

    /**
     * @return array{serviceId: int, parcelsCount: int}
     */
    public function toArray(): array
    {
        return [
            'serviceId' => $this->serviceId,
            'parcelsCount' => $this->parcelsCount,
        ];
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            serviceId: (int) ($data['serviceId'] ?? 0),
            parcelsCount: (int) ($data['parcelsCount'] ?? 0),
        );
    }
}
