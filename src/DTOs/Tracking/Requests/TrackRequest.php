<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Tracking\Requests;

use Daika7ana\Dpd\DTOs\Tracking\Models\TrackShipmentParcelRef;
use Daika7ana\Dpd\Support\PayloadFilter;

final readonly class TrackRequest
{
    /**
     * @param list<TrackShipmentParcelRef> $parcels
     */
    public function __construct(
        public array $parcels,
        public ?bool $lastOperationOnly = null,
    ) {}

    /**
     * @return array<string, list<array<string, string>>|bool>
     */
    public function toArray(): array
    {
        return PayloadFilter::removeNulls([
            'parcels' => array_map(
                static fn(TrackShipmentParcelRef $parcel): array => $parcel->toArray(),
                $this->parcels,
            ),
            'lastOperationOnly' => $this->lastOperationOnly,
        ]);
    }
}
