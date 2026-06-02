<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Locations\Offices;

final readonly class OfficeWorkingTimeSchedule
{
    public function __construct(
        public bool $standardSchedule,
        public ?string $date = null,
        public ?string $workingTimeFrom = null,
        public ?string $workingTimeTo = null,
        public ?string $sameDayDepartureCutoff = null,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            standardSchedule: (bool) ($data['standardSchedule'] ?? false),
            date: isset($data['date']) ? (string) $data['date'] : null,
            workingTimeFrom: isset($data['workingTimeFrom']) ? (string) $data['workingTimeFrom'] : null,
            workingTimeTo: isset($data['workingTimeTo']) ? (string) $data['workingTimeTo'] : null,
            sameDayDepartureCutoff: isset($data['sameDayDepartureCutoff']) ? (string) $data['sameDayDepartureCutoff'] : null,
        );
    }
}
