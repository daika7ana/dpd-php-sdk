<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Shipments\Payloads;

use DateTimeInterface;
use Daika7ana\Dpd\Support\PayloadFilter;

final readonly class MidwayCarrierParcelHandover
{
    public function __construct(
        public string $id,
        public int $countryId,
        public DateTimeInterface $dateTime,
        public ?string $siteName = null,
    ) {}

    /**
     * @return array<string, int|string>
     */
    public function toArray(): array
    {
        return PayloadFilter::removeNulls([
            'id' => $this->id,
            'countryId' => $this->countryId,
            'dateTime' => $this->dateTime->format('Y-m-d\\TH:i:sP'),
            'siteName' => $this->siteName,
        ]);
    }
}
