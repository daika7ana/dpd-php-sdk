<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Shipments\AdditionalServices;

use Daika7ana\Dpd\Support\PayloadFilter;

final readonly class ShipmentDeclaredValueAdditionalService
{
    public function __construct(
        public float $amount,
        public ?bool $fragile = null,
        public ?bool $ignoreIfNotApplicable = null,
    ) {}

    /**
     * @return array<string, bool|float>
     */
    public function toArray(): array
    {
        return PayloadFilter::removeNulls([
            'amount' => $this->amount,
            'fragile' => $this->fragile,
            'ignoreIfNotApplicable' => $this->ignoreIfNotApplicable,
        ]);
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            amount: (float) ($data['amount'] ?? 0.0),
            fragile: isset($data['fragile']) ? (bool) $data['fragile'] : null,
            ignoreIfNotApplicable: isset($data['ignoreIfNotApplicable']) ? (bool) $data['ignoreIfNotApplicable'] : null,
        );
    }
}
