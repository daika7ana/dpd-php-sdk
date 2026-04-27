<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Shipments\Responses;

use Daika7ana\Dpd\Enums\Shipments\SecondaryShipmentType;

final readonly class PrimaryShipment
{
    public function __construct(
        public string $id,
        public SecondaryShipmentType $type,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: (string) ($data['id'] ?? ''),
            type: SecondaryShipmentType::from((string) ($data['type'] ?? SecondaryShipmentType::ReturnShipment->value)),
        );
    }
}
