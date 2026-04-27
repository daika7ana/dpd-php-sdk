<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Pickup\Requests;

use Daika7ana\Dpd\DTOs\Common\Addressing\ShipmentPhoneNumber;
use Daika7ana\Dpd\Enums\Pickup\PickupScope;
use Daika7ana\Dpd\Support\PayloadFilter;
use DateTimeInterface;

final readonly class PickupRequest
{
    /**
     * @param list<string> $explicitShipmentIdList
     */
    public function __construct(
        public string $visitEndTime,
        public ?DateTimeInterface $pickupDateTime = null,
        public ?bool $autoAdjustPickupDate = null,
        public ?PickupScope $pickupScope = null,
        public array $explicitShipmentIdList = [],
        public ?string $contactName = null,
        public ?ShipmentPhoneNumber $phoneNumber = null,
    ) {}

    /**
     * @return array<string, array<string, string>|bool|list<string>|string>
     */
    public function toArray(): array
    {
        return PayloadFilter::removeNulls([
            'pickupDateTime' => $this->pickupDateTime?->format('Y-m-d\TH:i:sP'),
            'autoAdjustPickupDate' => $this->autoAdjustPickupDate,
            'pickupScope' => $this->pickupScope?->value,
            'explicitShipmentIdList' => $this->explicitShipmentIdList,
            'visitEndTime' => $this->visitEndTime,
            'contactName' => $this->contactName,
            'phoneNumber' => $this->phoneNumber?->toArray(),
        ]);
    }
}
