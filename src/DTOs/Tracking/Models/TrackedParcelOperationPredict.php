<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Tracking\Models;

final readonly class TrackedParcelOperationPredict
{
    public function __construct(
        public string $predictedVisitDateTimeFrom,
        public string $predictedVisitDateTimeTo,
        public ?int $includedDelayInMinutes = null,
        public bool $canceled = false,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            predictedVisitDateTimeFrom: (string) ($data['predictedVisitDateTimeFrom'] ?? ''),
            predictedVisitDateTimeTo: (string) ($data['predictedVisitDateTimeTo'] ?? ''),
            includedDelayInMinutes: isset($data['includedDelayInMinutes']) ? (int) $data['includedDelayInMinutes'] : null,
            canceled: (bool) ($data['canceled'] ?? false),
        );
    }
}
