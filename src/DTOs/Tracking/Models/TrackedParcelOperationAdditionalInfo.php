<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Tracking\Models;

use Daika7ana\Dpd\Enums\Locations\OfficeType;

final readonly class TrackedParcelOperationAdditionalInfo
{
    public function __construct(
        public ?string $officeUrl = null,
        public ?TrackedParcelOperationAdditionalInfoRecipient $recipient = null,
        public ?string $unsuccessfulDeliveryStickerImageUrl = null,
        public ?OfficeType $pickupOfficeType = null,
        public ?string $geoPUDOId = null,
        public ?TrackedParcelOperationPredict $predict = null,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            officeUrl: isset($data['officeURL']) ? (string) $data['officeURL'] : null,
            recipient: is_array($data['recipient'] ?? null) ? TrackedParcelOperationAdditionalInfoRecipient::fromArray($data['recipient']) : null,
            unsuccessfulDeliveryStickerImageUrl: isset($data['unsuccessfulDeliveryStickerImageURL']) ? (string) $data['unsuccessfulDeliveryStickerImageURL'] : null,
            pickupOfficeType: isset($data['pickupOfficeType']) ? OfficeType::tryFrom((string) $data['pickupOfficeType']) : null,
            geoPUDOId: isset($data['geoPUDOId']) ? (string) $data['geoPUDOId'] : null,
            predict: is_array($data['predict'] ?? null) ? TrackedParcelOperationPredict::fromArray($data['predict']) : null,
        );
    }
}
