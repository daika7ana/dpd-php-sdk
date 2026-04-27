<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Shipments\Responses;

final readonly class ReturnAmounts
{
    public function __construct(
        public ?MoneyTransferPremium $moneyTransfer = null,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            moneyTransfer: isset($data['moneyTransfer']) && is_array($data['moneyTransfer'])
                ? MoneyTransferPremium::fromArray($data['moneyTransfer'])
                : null,
        );
    }
}
