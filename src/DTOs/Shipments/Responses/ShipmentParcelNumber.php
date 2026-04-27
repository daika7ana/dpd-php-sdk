<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Shipments\Responses;

final readonly class ShipmentParcelNumber
{
    public function __construct(
        public string $id,
        public int $seqNo,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: (string) ($data['id'] ?? ''),
            seqNo: (int) ($data['seqNo'] ?? 0),
        );
    }
}
