<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Shipments\Payloads;

use Daika7ana\Dpd\DTOs\Common\Addressing\ShipmentParcelRef;
use DateTimeInterface;

final readonly class ParcelHandover
{
    public function __construct(
        public ShipmentParcelRef $parcel,
        public DateTimeInterface $dateTime,
    ) {}

    /**
     * @return array<string, string>
     */
    public function toArray(): array
    {
        return [
            ...$this->parcel->toArray(),
            'dateTime' => $this->dateTime->format('Y-m-d\\TH:i:sP'),
        ];
    }
}
