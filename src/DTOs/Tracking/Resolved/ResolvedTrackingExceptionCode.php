<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Tracking\Resolved;

final readonly class ResolvedTrackingExceptionCode
{
    /**
     * @param list<int> $operationCodes
     */
    public function __construct(
        public int $code,
        public string $description,
        public array $operationCodes,
    ) {}

    public function isKnown(): bool
    {
        return $this->description !== 'Unknown tracking exception code';
    }
}
