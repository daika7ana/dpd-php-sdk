<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Shipments\AdditionalServices;

use Daika7ana\Dpd\Support\PayloadFilter;

final readonly class ShipmentRopAdditionalService
{
    /**
     * @param list<ShipmentRopAdditionalServiceLine> $pallets
     */
    public function __construct(
        public array $pallets,
        public ?bool $thirdPartyPayer = null,
    ) {}

    /**
     * @return array<string, array<int, array<string, int>>|bool>
     */
    public function toArray(): array
    {
        return PayloadFilter::removeNulls([
            'pallets' => array_map(
                static fn(ShipmentRopAdditionalServiceLine $line): array => $line->toArray(),
                $this->pallets,
            ),
            'thirdPartyPayer' => $this->thirdPartyPayer,
        ]);
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $pallets = array_map(
            static fn(array $line): ShipmentRopAdditionalServiceLine => ShipmentRopAdditionalServiceLine::fromArray($line),
            array_values(array_filter($data['pallets'] ?? [], static fn(mixed $line): bool => is_array($line))),
        );

        return new self(
            pallets: $pallets,
            thirdPartyPayer: isset($data['thirdPartyPayer']) ? (bool) $data['thirdPartyPayer'] : null,
        );
    }
}
