<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Shipments\AdditionalServices;

use Daika7ana\Dpd\Enums\Shipments\CodProcessingType;
use Daika7ana\Dpd\Support\PayloadFilter;

final readonly class ShipmentCodAdditionalService
{
    /**
     * @param list<ShipmentCodFiscalReceiptItem> $fiscalReceiptItems
     */
    public function __construct(
        public float $amount,
        public array $fiscalReceiptItems = [],
        public ?string $currencyCode = null,
        public ?CodProcessingType $processingType = null,
        public ?bool $payoutToThirdParty = null,
        public ?bool $payoutToLoggedClient = null,
        public ?bool $includeShippingPrice = null,
        public ?bool $cardPaymentForbidden = null,
    ) {}

    /**
     * @return array<string, array<int, array<string, float|string>>|bool|float|string>
     */
    public function toArray(): array
    {
        return PayloadFilter::removeNullsAndEmptyArrays([
            'amount' => $this->amount,
            'currencyCode' => $this->currencyCode,
            'processingType' => $this->processingType?->value,
            'payoutToThirdParty' => $this->payoutToThirdParty,
            'payoutToLoggedClient' => $this->payoutToLoggedClient,
            'includeShippingPrice' => $this->includeShippingPrice,
            'cardPaymentForbidden' => $this->cardPaymentForbidden,
            'fiscalReceiptItems' => array_map(
                static fn(ShipmentCodFiscalReceiptItem $item): array => $item->toArray(),
                $this->fiscalReceiptItems,
            ),
        ]);
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $fiscalReceiptItems = array_map(
            static fn(array $item): ShipmentCodFiscalReceiptItem => ShipmentCodFiscalReceiptItem::fromArray($item),
            array_values(array_filter($data['fiscalReceiptItems'] ?? [], static fn(mixed $item): bool => is_array($item))),
        );

        return new self(
            amount: (float) ($data['amount'] ?? 0.0),
            fiscalReceiptItems: $fiscalReceiptItems,
            currencyCode: isset($data['currencyCode']) ? (string) $data['currencyCode'] : null,
            processingType: isset($data['processingType']) ? CodProcessingType::tryFrom((string) $data['processingType']) : null,
            payoutToThirdParty: isset($data['payoutToThirdParty']) ? (bool) $data['payoutToThirdParty'] : null,
            payoutToLoggedClient: isset($data['payoutToLoggedClient']) ? (bool) $data['payoutToLoggedClient'] : null,
            includeShippingPrice: isset($data['includeShippingPrice']) ? (bool) $data['includeShippingPrice'] : null,
            cardPaymentForbidden: isset($data['cardPaymentForbidden']) ? (bool) $data['cardPaymentForbidden'] : null,
        );
    }
}
