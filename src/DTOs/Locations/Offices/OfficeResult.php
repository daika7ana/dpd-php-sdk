<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Locations\Offices;

final readonly class OfficeResult
{
    public function __construct(
        public Office $office,
        public ?int $distance,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            office: Office::fromArray($data),
            distance: isset($data['distance']) ? (int) $data['distance'] : null,
        );
    }
}
