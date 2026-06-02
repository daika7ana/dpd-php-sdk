<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Tracking\Models;

final readonly class TrackedParcelOperationPredict
{
    public function __construct(
        public string $predictedVisitDateTimeFrom,
        public string $predictedVisitDateTimeTo,
        public bool $canceled = false,
        public ?int $includedDelayInMinutes = null,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            predictedVisitDateTimeFrom: (string) ($data['predictedVisitDateTimeFrom'] ?? ''),
            predictedVisitDateTimeTo: (string) ($data['predictedVisitDateTimeTo'] ?? ''),
            canceled: (bool) ($data['canceled'] ?? false),
            includedDelayInMinutes: isset($data['includedDelayInMinutes']) ? (int) $data['includedDelayInMinutes'] : null,
        );
    }
}
