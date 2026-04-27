<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Locations\Requests;

use Daika7ana\Dpd\Support\PayloadFilter;

final readonly class SiteSearchRequest
{
    public function __construct(
        public int $countryId,
        public ?string $name = null,
        public ?string $postCode = null,
        public ?string $type = null,
        public ?string $municipality = null,
        public ?string $region = null,
    ) {}

    /**
     * @return array<string, int|string>
     */
    public function toArray(): array
    {
        return PayloadFilter::removeNulls([
            'countryId' => $this->countryId,
            'name' => $this->name,
            'postCode' => $this->postCode,
            'type' => $this->type,
            'municipality' => $this->municipality,
            'region' => $this->region,
        ]);
    }
}
