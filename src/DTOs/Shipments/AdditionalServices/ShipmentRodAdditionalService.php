<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Shipments\AdditionalServices;

use Daika7ana\Dpd\Support\PayloadFilter;

final readonly class ShipmentRodAdditionalService
{
    public function __construct(
        public bool $enabled,
        public ?string $comment = null,
        public ?int $returnToClientId = null,
        public ?int $returnToOfficeId = null,
        public ?bool $thirdPartyPayer = null,
    ) {}

    /**
     * @return array<string, bool|int|string>
     */
    public function toArray(): array
    {
        return PayloadFilter::removeNulls([
            'enabled' => $this->enabled,
            'comment' => $this->comment,
            'returnToClientId' => $this->returnToClientId,
            'returnToOfficeId' => $this->returnToOfficeId,
            'thirdPartyPayer' => $this->thirdPartyPayer,
        ]);
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            enabled: (bool) ($data['enabled'] ?? false),
            comment: isset($data['comment']) ? (string) $data['comment'] : null,
            returnToClientId: isset($data['returnToClientId']) ? (int) $data['returnToClientId'] : null,
            returnToOfficeId: isset($data['returnToOfficeId']) ? (int) $data['returnToOfficeId'] : null,
            thirdPartyPayer: isset($data['thirdPartyPayer']) ? (bool) $data['thirdPartyPayer'] : null,
        );
    }
}
