<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Common\Documents;

final readonly class LabelInfo
{
    public function __construct(
        public string $parcelId,
        public ?int $hubId,
        public ?int $officeId,
        public ?string $officeName,
        public ?int $deadlineDay,
        public ?int $deadlineMonth,
        public ?int $tourId,
        public string $fullBarcode,
        public int $exportPriority,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            parcelId: (string) ($data['parcelId'] ?? ''),
            hubId: isset($data['hubId']) ? (int) $data['hubId'] : null,
            officeId: isset($data['officeId']) ? (int) $data['officeId'] : null,
            officeName: isset($data['officeName']) ? (string) $data['officeName'] : null,
            deadlineDay: isset($data['deadlineDay']) ? (int) $data['deadlineDay'] : null,
            deadlineMonth: isset($data['deadlineMonth']) ? (int) $data['deadlineMonth'] : null,
            tourId: isset($data['tourId']) ? (int) $data['tourId'] : null,
            fullBarcode: (string) ($data['fullBarcode'] ?? ''),
            exportPriority: (int) ($data['exportPriority'] ?? 0),
        );
    }
}
