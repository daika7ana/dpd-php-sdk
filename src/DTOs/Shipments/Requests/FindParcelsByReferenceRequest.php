<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Shipments\Requests;

use Daika7ana\Dpd\Enums\Shipments\SearchInReference;
use Daika7ana\Dpd\Support\PayloadFilter;
use DateTimeInterface;

final readonly class FindParcelsByReferenceRequest
{
    public function __construct(
        public string $ref,
        public SearchInReference $searchInRef,
        public ?bool $shipmentsOnly = null,
        public ?bool $includeReturns = null,
        public ?DateTimeInterface $fromDateTime = null,
        public ?DateTimeInterface $toDateTime = null,
    ) {}

    /**
     * @return array<string, bool|int|string>
     */
    public function toArray(): array
    {
        return PayloadFilter::removeNulls([
            'ref' => $this->ref,
            'searchInRef' => $this->searchInRef->value,
            'shipmentsOnly' => $this->shipmentsOnly,
            'includeReturns' => $this->includeReturns,
            'fromDateTime' => $this->fromDateTime?->format('Y-m-d\\TH:i:sP'),
            'toDateTime' => $this->toDateTime?->format('Y-m-d\\TH:i:sP'),
        ]);
    }
}
