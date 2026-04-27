<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Tracking\Models;

final readonly class BulkTrackingDataFile
{
    public function __construct(
        public int $id,
        public string $url,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: (int) ($data['id'] ?? 0),
            url: (string) ($data['url'] ?? ''),
        );
    }
}
