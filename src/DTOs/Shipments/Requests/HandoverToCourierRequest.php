<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Shipments\Requests;

use Daika7ana\Dpd\DTOs\Shipments\Payloads\ParcelHandover;

final readonly class HandoverToCourierRequest
{
    /**
     * @param list<ParcelHandover> $parcels
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
            'parcels' => array_map(static fn(ParcelHandover $parcel): array => $parcel->toArray(), $this->parcels),
        ];
    }
}
