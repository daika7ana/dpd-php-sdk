<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Locations\Requests;

use Daika7ana\Dpd\Support\PayloadFilter;

final readonly class BlockSearchRequest
{
    public function __construct(
        public int $siteId,
        public ?string $name = null,
    ) {}

    /**
     * @return array<string, int|string>
     */
    public function toArray(): array
    {
        return PayloadFilter::removeNulls([
            'siteId' => $this->siteId,
            'name' => $this->name,
        ]);
    }
}
