<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Payments\Payouts;

use Daika7ana\Dpd\DTOs\Common\Support\Collection;
use Daika7ana\Dpd\Enums\Payments\PayoutDocumentType;
use Daika7ana\Dpd\Enums\Payments\PayoutPaymentType;

final readonly class Payout
{
    /**
     * @param Collection<int, PayoutDetail> $details
     */
    public function __construct(
        public string $date,
        public int $docId,
        public PayoutDocumentType $docType,
        public PayoutPaymentType $paymentType,
        public string $payee,
        public string $currency,
        public float $amount,
        public Collection $details,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $details = array_map(
            static fn(array $detail): PayoutDetail => PayoutDetail::fromArray($detail),
            array_values(array_filter($data['details'] ?? [], static fn(mixed $detail): bool => is_array($detail))),
        );

        return new self(
            date: (string) ($data['date'] ?? ''),
            docId: (int) ($data['docId'] ?? 0),
            docType: PayoutDocumentType::from((string) ($data['docType'] ?? PayoutDocumentType::Cash->value)),
            paymentType: PayoutPaymentType::from((string) ($data['paymentType'] ?? PayoutPaymentType::Cash->value)),
            payee: (string) ($data['payee'] ?? ''),
            currency: (string) ($data['currency'] ?? ''),
            amount: (float) ($data['amount'] ?? 0.0),
            details: new Collection($details),
        );
    }
}
