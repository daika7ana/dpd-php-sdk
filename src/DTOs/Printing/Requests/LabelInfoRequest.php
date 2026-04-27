<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Printing\Requests;

use Daika7ana\Dpd\DTOs\Common\Addressing\ShipmentParcelRef;

final readonly class LabelInfoRequest
{
    /**
     * @param list<ShipmentParcelRef> $parcels
     */
    public function __construct(
        public array $parcels,
    ) {}

    /**
     * @return array{parcels: list<array<string, string>>}
     */
    public function toArray(): array
    {
        return [
            'parcels' => array_map(
                static fn(ShipmentParcelRef $parcel): array => $parcel->toArray(),
                $this->parcels,
            ),
        ];
    }
}
