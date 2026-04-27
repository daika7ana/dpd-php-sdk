<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Calculations\Requests;

use Daika7ana\Dpd\DTOs\Calculations\Payloads\CalculationContent;
use Daika7ana\Dpd\DTOs\Calculations\Payloads\CalculationRecipient;
use Daika7ana\Dpd\DTOs\Calculations\Payloads\CalculationSender;
use Daika7ana\Dpd\DTOs\Calculations\Payloads\CalculationService;
use Daika7ana\Dpd\DTOs\Shipments\Payloads\ShipmentPayment;
use Daika7ana\Dpd\Support\PayloadFilter;

final readonly class CalculationRequest
{
    public function __construct(
        public CalculationRecipient $recipient,
        public CalculationService $service,
        public CalculationContent $content,
        public ShipmentPayment $payment,
        public ?CalculationSender $sender = null,
    ) {}

    /**
     * @return array<string, array<string, mixed>>
     */
    public function toArray(): array
    {
        return PayloadFilter::removeNulls([
            'sender' => $this->sender?->toArray(),
            'recipient' => $this->recipient->toArray(),
            'service' => $this->service->toArray(),
            'content' => $this->content->toArray(),
            'payment' => $this->payment->toArray(),
        ]);
    }
}
