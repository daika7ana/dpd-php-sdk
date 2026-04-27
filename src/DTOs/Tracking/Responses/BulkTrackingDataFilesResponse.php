<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Tracking\Responses;

use Daika7ana\Dpd\DTOs\Common\Support\Collection;
use Daika7ana\Dpd\DTOs\Tracking\Models\BulkTrackingDataFile;

final readonly class BulkTrackingDataFilesResponse
{
    /**
     * @param Collection<int, BulkTrackingDataFile> $files
     */
    public function __construct(
        public Collection $files,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $files = array_map(
            static fn(array $file): BulkTrackingDataFile => BulkTrackingDataFile::fromArray($file),
            array_values(array_filter($data['files'] ?? [], static fn(mixed $file): bool => is_array($file))),
        );

        return new self(new Collection($files));
    }

    public function nextLastProcessedFileId(): ?int
    {
        $lastFile = $this->files->last();

        return $lastFile instanceof BulkTrackingDataFile ? $lastFile->id : null;
    }
}
