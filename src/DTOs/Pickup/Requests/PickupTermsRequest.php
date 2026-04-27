<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Pickup\Requests;

use Daika7ana\Dpd\DTOs\Calculations\Payloads\CalculationSender;
use Daika7ana\Dpd\Support\PayloadFilter;
use DateTimeInterface;

final readonly class PickupTermsRequest
{
    public function __construct(
        public int $serviceId,
        public ?DateTimeInterface $startingDate = null,
        public ?CalculationSender $sender = null,
        public ?bool $senderHasPayment = null,
    ) {}

    /**
     * @return array<string, array<string, mixed>|bool|int|string>
     */
    public function toArray(): array
    {
        return PayloadFilter::removeNulls([
            'serviceId' => $this->serviceId,
            'startingDate' => $this->startingDate?->format('Y-m-d'),
            'sender' => $this->sender?->toArray(),
            'senderHasPayment' => $this->senderHasPayment,
        ]);
    }
}
