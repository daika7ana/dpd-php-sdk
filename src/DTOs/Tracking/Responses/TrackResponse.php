<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Tracking\Responses;

use Daika7ana\Dpd\DTOs\Common\Support\Collection;
use Daika7ana\Dpd\DTOs\Tracking\Models\TrackedParcel;

final readonly class TrackResponse
{
    /**
     * @param Collection<int, TrackedParcel> $parcels
     */
    public function __construct(
        public Collection $parcels,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $parcels = array_map(
            static fn(array $parcel): TrackedParcel => TrackedParcel::fromArray($parcel),
            array_values(array_filter($data['parcels'] ?? [], static fn(mixed $parcel): bool => is_array($parcel))),
        );

        return new self(new Collection($parcels));
    }

    public function merge(self $other): self
    {
        return new self(new Collection([
            ...$this->parcels->all(),
            ...$other->parcels->all(),
        ]));
    }
}
