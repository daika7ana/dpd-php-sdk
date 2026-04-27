<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Tracking\Requests;

final readonly class BulkTrackingDataFilesRequest
{
    public function __construct(
        public int $lastProcessedFileId,
    ) {}

    /**
     * @return array{lastProcessedFileId: int}
     */
    public function toArray(): array
    {
        return ['lastProcessedFileId' => $this->lastProcessedFileId];
    }
}
