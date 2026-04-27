<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Calculations\Payloads;

use Daika7ana\Dpd\DTOs\Common\Addressing\AddressLocation;
use Daika7ana\Dpd\Support\PayloadFilter;

final readonly class CalculationSender
{
    public function __construct(
        public ?int $clientId = null,
        public ?bool $privatePerson = null,
        public ?AddressLocation $addressLocation = null,
        public ?int $dropoffOfficeId = null,
        public ?string $dropoffGeoPUDOId = null,
    ) {}

    /**
     * @return array<string, array<string, float|int|string>|bool|int|string>
     */
    public function toArray(): array
    {
        return PayloadFilter::removeNulls([
            'clientId' => $this->clientId,
            'privatePerson' => $this->privatePerson,
            'addressLocation' => $this->addressLocation?->toArray(),
            'dropoffOfficeId' => $this->dropoffOfficeId,
            'dropoffGeoPUDOId' => $this->dropoffGeoPUDOId,
        ]);
    }
}
