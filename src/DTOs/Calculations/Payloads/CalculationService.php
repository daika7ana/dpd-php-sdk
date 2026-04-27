<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Calculations\Payloads;

use Daika7ana\Dpd\DTOs\Shipments\AdditionalServices\ShipmentAdditionalServices;
use Daika7ana\Dpd\Support\PayloadFilter;
use DateTimeInterface;

final readonly class CalculationService
{
    /**
     * @param list<int> $serviceIds
     */
    public function __construct(
        public array $serviceIds,
        public ?DateTimeInterface $pickupDate = null,
        public ?bool $autoAdjustPickupDate = null,
        public ?ShipmentAdditionalServices $additionalServices = null,
        public ?int $deferredDays = null,
        public ?bool $saturdayDelivery = null,
    ) {}

    /**
     * @return array<string, array<string, mixed>|bool|int|list<int>|string>
     */
    public function toArray(): array
    {
        return PayloadFilter::removeNulls([
            'pickupDate' => $this->pickupDate?->format('Y-m-d'),
            'autoAdjustPickupDate' => $this->autoAdjustPickupDate,
            'serviceIds' => $this->serviceIds,
            'additionalServices' => $this->additionalServices?->toArray(),
            'deferredDays' => $this->deferredDays,
            'saturdayDelivery' => $this->saturdayDelivery,
        ]);
    }
}
