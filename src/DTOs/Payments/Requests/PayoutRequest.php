<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Payments\Requests;

use Daika7ana\Dpd\Support\PayloadFilter;
use DateTimeInterface;

final readonly class PayoutRequest
{
    public function __construct(
        public DateTimeInterface $fromDate,
        public DateTimeInterface $toDate,
        public ?bool $includeDetails = null,
    ) {}

    /**
     * @return array<string, bool|string>
     */
    public function toArray(): array
    {
        return PayloadFilter::removeNulls([
            'fromDate' => $this->fromDate->format('Y-m-d\TH:i:sP'),
            'toDate' => $this->toDate->format('Y-m-d\TH:i:sP'),
            'includeDetails' => $this->includeDetails,
        ]);
    }
}
