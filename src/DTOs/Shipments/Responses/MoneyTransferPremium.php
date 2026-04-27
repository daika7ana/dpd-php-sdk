<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Shipments\Responses;

use Daika7ana\Dpd\Enums\Payments\Payer;

final readonly class MoneyTransferPremium
{
    public function __construct(
        public ?float $amount = null,
        public ?float $amountLocal = null,
        public ?Payer $payer = null,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            amount: isset($data['amount']) ? (float) $data['amount'] : null,
            amountLocal: isset($data['amountLocal']) ? (float) $data['amountLocal'] : null,
            payer: isset($data['payer']) ? Payer::from((string) $data['payer']) : null,
        );
    }
}
