<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Common\Documents;

final readonly class LabelInfo
{
    public function __construct(
        public string $parcelId,
        public string $fullBarcode,
        public int $exportPriority,
        public ?int $hubId = null,
        public ?int $officeId = null,
        public ?string $officeName = null,
        public ?int $deadlineDay = null,
        public ?int $deadlineMonth = null,
        public ?int $tourId = null,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            parcelId: (string) ($data['parcelId'] ?? ''),
            fullBarcode: (string) ($data['fullBarcode'] ?? ''),
            exportPriority: (int) ($data['exportPriority'] ?? 0),
            hubId: isset($data['hubId']) ? (int) $data['hubId'] : null,
            officeId: isset($data['officeId']) ? (int) $data['officeId'] : null,
            officeName: isset($data['officeName']) ? (string) $data['officeName'] : null,
            deadlineDay: isset($data['deadlineDay']) ? (int) $data['deadlineDay'] : null,
            deadlineMonth: isset($data['deadlineMonth']) ? (int) $data['deadlineMonth'] : null,
            tourId: isset($data['tourId']) ? (int) $data['tourId'] : null,
        );
    }
}
