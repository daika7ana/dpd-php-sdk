<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Shipments\Responses;

use Daika7ana\Dpd\DTOs\Shipments\Payloads\ShipmentDiscountCardId;
use Daika7ana\Dpd\Enums\Payments\Payer;

final readonly class ShipmentPaymentRecord
{
    public function __construct(
        public Payer $courierServicePayer,
        public ?Payer $declaredValuePayer = null,
        public ?Payer $packagePayer = null,
        public ?int $thirdPartyClientId = null,
        public ?ShipmentDiscountCardId $discountCardId = null,
        public ?CodPayment $codPayment = null,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            courierServicePayer: Payer::from((string) ($data['courierServicePayer'] ?? Payer::Sender->value)),
            declaredValuePayer: isset($data['declaredValuePayer']) ? Payer::from((string) $data['declaredValuePayer']) : null,
            packagePayer: isset($data['packagePayer']) ? Payer::from((string) $data['packagePayer']) : null,
            thirdPartyClientId: isset($data['thirdPartyClientId']) ? (int) $data['thirdPartyClientId'] : null,
            discountCardId: isset($data['discountCardId']) && is_array($data['discountCardId']) ? ShipmentDiscountCardId::fromArray($data['discountCardId']) : null,
            codPayment: isset($data['codPayment']) && is_array($data['codPayment']) ? CodPayment::fromArray($data['codPayment']) : null,
        );
    }
}
