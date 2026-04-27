<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Shipments\Payloads;

use Daika7ana\Dpd\DTOs\Common\Addressing\BankAccount;
use Daika7ana\Dpd\Enums\Payments\Payer;
use Daika7ana\Dpd\Support\PayloadFilter;

final readonly class ShipmentPayment
{
    public function __construct(
        public Payer $courierServicePayer,
        public ?Payer $declaredValuePayer = null,
        public ?Payer $packagePayer = null,
        public ?int $thirdPartyClientId = null,
        public ?ShipmentDiscountCardId $discountCardId = null,
        public ?BankAccount $senderBankAccount = null,
        public ?bool $administrativeFee = null,
    ) {}

    /**
     * @return array<string, array<string, int|string>|bool|int|string>
     */
    public function toArray(): array
    {
        return PayloadFilter::removeNulls([
            'courierServicePayer' => $this->courierServicePayer->value,
            'declaredValuePayer' => $this->declaredValuePayer?->value,
            'packagePayer' => $this->packagePayer?->value,
            'thirdPartyClientId' => $this->thirdPartyClientId,
            'discountCardId' => $this->discountCardId?->toArray(),
            'senderBankAccount' => $this->senderBankAccount?->toArray(),
            'administrativeFee' => $this->administrativeFee,
        ]);
    }
}
