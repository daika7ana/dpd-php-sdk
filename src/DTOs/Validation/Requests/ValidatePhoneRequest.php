<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Validation\Requests;

use Daika7ana\Dpd\Support\PayloadFilter;

final readonly class ValidatePhoneRequest
{
    public function __construct(
        public string $number,
        public ?string $ext = null,
    ) {}

    /**
     * @return array<string, string>
     */
    public function toArray(): array
    {
        return PayloadFilter::removeNulls([
            'number' => $this->number,
            'ext' => $this->ext,
        ]);
    }
}
