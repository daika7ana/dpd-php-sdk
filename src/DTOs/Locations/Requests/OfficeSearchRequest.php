<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Locations\Requests;

use Daika7ana\Dpd\Support\PayloadFilter;

final readonly class OfficeSearchRequest
{
    public function __construct(
        public ?int $countryId = null,
        public ?int $siteId = null,
        public ?string $siteName = null,
        public ?string $name = null,
        public ?int $limit = null,
    ) {}

    /**
     * @return array<string, int|string>
     */
    public function toArray(): array
    {
        return PayloadFilter::removeNulls([
            'countryId' => $this->countryId,
            'siteId' => $this->siteId,
            'siteName' => $this->siteName,
            'name' => $this->name,
            'limit' => $this->limit,
        ]);
    }
}
