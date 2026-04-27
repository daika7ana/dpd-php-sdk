<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Calculations\Requests;

use Daika7ana\Dpd\DTOs\Calculations\Payloads\CalculationRecipient;
use Daika7ana\Dpd\DTOs\Calculations\Payloads\CalculationSender;
use Daika7ana\Dpd\Support\PayloadFilter;
use DateTimeInterface;

final readonly class DestinationServicesRequest
{
    public function __construct(
        public CalculationRecipient $recipient,
        public ?DateTimeInterface $date = null,
        public ?CalculationSender $sender = null,
    ) {}

    /**
     * @return array<string, array<string, mixed>|string>
     */
    public function toArray(): array
    {
        return PayloadFilter::removeNulls([
            'date' => $this->date?->format('Y-m-d'),
            'sender' => $this->sender?->toArray(),
            'recipient' => $this->recipient->toArray(),
        ]);
    }
}
