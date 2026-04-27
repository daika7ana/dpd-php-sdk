<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Tracking\Models;

final readonly class ExternalCarrierParcelNumberDetails
{
    public function __construct(
        public int $externalCarrierId,
        public string $externalCarrierName,
        public ?string $trackAndTraceUrl = null,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            externalCarrierId: (int) ($data['externalCarrierId'] ?? 0),
            externalCarrierName: (string) ($data['externalCarrierName'] ?? ''),
            trackAndTraceUrl: isset($data['trackAndTraceUrl']) ? (string) $data['trackAndTraceUrl'] : null,
        );
    }
}
