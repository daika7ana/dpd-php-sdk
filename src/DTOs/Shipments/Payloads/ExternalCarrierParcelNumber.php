<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Shipments\Payloads;

use Daika7ana\Dpd\Enums\Shipments\ExternalCarrier;

final readonly class ExternalCarrierParcelNumber
{
    public function __construct(
        public ExternalCarrier $externalCarrier,
        public string $parcelNumber,
    ) {}

    /**
     * @return array{externalCarrier: string, parcelNumber: string}
     */
    public function toArray(): array
    {
        return [
            'externalCarrier' => $this->externalCarrier->value,
            'parcelNumber' => $this->parcelNumber,
        ];
    }
}
