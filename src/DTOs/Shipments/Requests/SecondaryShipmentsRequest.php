<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Shipments\Requests;

use Daika7ana\Dpd\Enums\Shipments\SecondaryShipmentType;

final readonly class SecondaryShipmentsRequest
{
    /**
     * @param list<SecondaryShipmentType> $types
     */
    public function __construct(
        public string $shipmentId,
        public array $types = [],
    ) {}

    /**
     * @return array{types?: list<string>}
     */
    public function toArray(): array
    {
        $types = array_map(static fn(SecondaryShipmentType $type): string => $type->value, $this->types);

        return $types === []
            ? []
            : ['types' => $types];
    }
}
