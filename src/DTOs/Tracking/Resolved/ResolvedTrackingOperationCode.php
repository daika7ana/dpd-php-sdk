<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Tracking\Resolved;

use Daika7ana\Dpd\Enums\Tracking\TrackingOperationCode;

final readonly class ResolvedTrackingOperationCode
{
    public function __construct(
        public int $code,
        public string $description,
        public ?TrackingOperationCode $knownCode = null,
    ) {}

    public function isKnown(): bool
    {
        return $this->knownCode !== null;
    }
}
