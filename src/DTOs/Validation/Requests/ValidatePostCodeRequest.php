<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Validation\Requests;

use Daika7ana\Dpd\Support\PayloadFilter;

final readonly class ValidatePostCodeRequest
{
    public function __construct(
        public string $postCode,
        public ?int $countryId = null,
        public ?int $siteId = null,
    ) {}

    /**
     * @return array<string, int|string>
     */
    public function toArray(): array
    {
        return PayloadFilter::removeNulls([
            'countryId' => $this->countryId,
            'siteId' => $this->siteId,
            'postCode' => $this->postCode,
        ]);
    }
}
