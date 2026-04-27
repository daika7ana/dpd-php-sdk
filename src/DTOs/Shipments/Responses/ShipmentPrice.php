<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Shipments\Responses;

use Daika7ana\Dpd\DTOs\Common\Support\ShipmentPriceAmount;

final readonly class ShipmentPrice
{
    /**
     * @param array<string, ShipmentPriceAmount> $details
     * @param array<string, ShipmentPriceAmount> $detailsLocal
     */
    public function __construct(
        public float $amount,
        public float $vat,
        public float $total,
        public string $currency,
        public array $details,
        public float $amountLocal,
        public float $vatLocal,
        public float $totalLocal,
        public string $currencyLocal,
        public array $detailsLocal,
        public int $currencyExchangeRateUnit,
        public float $currencyExchangeRate,
        public ?ReturnAmounts $returnAmounts = null,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $details = [];
        foreach (($data['details'] ?? []) as $key => $value) {
            if (is_array($value)) {
                $details[(string) $key] = ShipmentPriceAmount::fromArray($value);
            }
        }

        $detailsLocal = [];
        foreach (($data['detailsLocal'] ?? []) as $key => $value) {
            if (is_array($value)) {
                $detailsLocal[(string) $key] = ShipmentPriceAmount::fromArray($value);
            }
        }

        return new self(
            amount: (float) ($data['amount'] ?? 0.0),
            vat: (float) ($data['vat'] ?? 0.0),
            total: (float) ($data['total'] ?? 0.0),
            currency: (string) ($data['currency'] ?? ''),
            details: $details,
            amountLocal: (float) ($data['amountLocal'] ?? 0.0),
            vatLocal: (float) ($data['vatLocal'] ?? 0.0),
            totalLocal: (float) ($data['totalLocal'] ?? 0.0),
            currencyLocal: (string) ($data['currencyLocal'] ?? ''),
            detailsLocal: $detailsLocal,
            currencyExchangeRateUnit: (int) ($data['currencyExchangeRateUnit'] ?? 0),
            currencyExchangeRate: (float) ($data['currencyExchangeRate'] ?? 0.0),
            returnAmounts: isset($data['returnAmounts']) && is_array($data['returnAmounts'])
                ? ReturnAmounts::fromArray($data['returnAmounts'])
                : null,
        );
    }
}
