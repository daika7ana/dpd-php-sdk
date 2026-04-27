<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Shipments\Requests;

use Daika7ana\Dpd\DTOs\Shipments\Payloads\MidwayCarrierParcelHandover;

final readonly class HandoverToMidwayCarrierRequest
{
    /**
     * @param list<MidwayCarrierParcelHandover> $parcels
     */
    public function __construct(
        public array $parcels,
    ) {}

    /**
     * @return array{parcels: list<array<string, int|string>>}
     */
    public function toArray(): array
    {
        return [
            'parcels' => array_map(static fn(MidwayCarrierParcelHandover $parcel): array => $parcel->toArray(), $this->parcels),
        ];
    }
}
