<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Shipments\Payloads;

use DateTimeImmutable;
use DateTimeInterface;
use Daika7ana\Dpd\DTOs\Shipments\AdditionalServices\ShipmentAdditionalServices;
use Daika7ana\Dpd\Support\PayloadFilter;

final readonly class ShipmentService
{
    public function __construct(
        public int $serviceId,
        public ?DateTimeInterface $pickupDate = null,
        public ?bool $autoAdjustPickupDate = null,
        public ?ShipmentAdditionalServices $additionalServices = null,
        public ?int $deferredDays = null,
        public ?bool $saturdayDelivery = null,
    ) {}

    /**
     * @return array<string, array<string, mixed>|bool|int|string>
     */
    public function toArray(): array
    {
        return PayloadFilter::removeNulls([
            'pickupDate' => $this->pickupDate?->format('Y-m-d'),
            'autoAdjustPickupDate' => $this->autoAdjustPickupDate,
            'serviceId' => $this->serviceId,
            'additionalServices' => $this->additionalServices?->toArray(),
            'deferredDays' => $this->deferredDays,
            'saturdayDelivery' => $this->saturdayDelivery,
        ]);
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            serviceId: (int) ($data['serviceId'] ?? $data['id'] ?? 0),
            pickupDate: isset($data['pickupDate']) ? new DateTimeImmutable((string) $data['pickupDate']) : null,
            autoAdjustPickupDate: isset($data['autoAdjustPickupDate']) ? (bool) $data['autoAdjustPickupDate'] : null,
            additionalServices: isset($data['additionalServices']) && is_array($data['additionalServices'])
                ? ShipmentAdditionalServices::fromArray($data['additionalServices'])
                : null,
            deferredDays: isset($data['deferredDays']) ? (int) $data['deferredDays'] : null,
            saturdayDelivery: isset($data['saturdayDelivery']) ? (bool) $data['saturdayDelivery'] : null,
        );
    }
}
