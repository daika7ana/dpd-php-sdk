<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Locations\Requests;

use Daika7ana\Dpd\Support\PayloadFilter;

final readonly class CountrySearchRequest
{
    public function __construct(
        public ?string $name = null,
        public ?string $isoAlpha2 = null,
        public ?string $isoAlpha3 = null,
    ) {}

    /**
     * @return array<string, string>
     */
    public function toArray(): array
    {
        return PayloadFilter::removeNulls([
            'name' => $this->name,
            'isoAlpha2' => $this->isoAlpha2,
            'isoAlpha3' => $this->isoAlpha3,
        ]);
    }
}
