<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Locations\Offices;

final readonly class OfficeWorkingTimeSchedule
{
    public function __construct(
        public ?string $date,
        public ?string $workingTimeFrom,
        public ?string $workingTimeTo,
        public ?string $sameDayDepartureCutoff,
        public bool $standardSchedule,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            date: isset($data['date']) ? (string) $data['date'] : null,
            workingTimeFrom: isset($data['workingTimeFrom']) ? (string) $data['workingTimeFrom'] : null,
            workingTimeTo: isset($data['workingTimeTo']) ? (string) $data['workingTimeTo'] : null,
            sameDayDepartureCutoff: isset($data['sameDayDepartureCutoff']) ? (string) $data['sameDayDepartureCutoff'] : null,
            standardSchedule: (bool) ($data['standardSchedule'] ?? false),
        );
    }
}
