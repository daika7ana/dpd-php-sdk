<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Shipments\AdditionalServices;

use Daika7ana\Dpd\Support\PayloadFilter;

final readonly class ShipmentElectronicReturnReceiptAdditionalService
{
    /**
     * @param list<string> $recipientEmails
     */
    public function __construct(
        public array $recipientEmails,
        public ?bool $thirdPartyPayer = null,
    ) {}

    /**
     * @return array<string, array<int, string>|bool>
     */
    public function toArray(): array
    {
        return PayloadFilter::removeNulls([
            'recipientEmails' => $this->recipientEmails,
            'thirdPartyPayer' => $this->thirdPartyPayer,
        ]);
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        /** @var list<string> $recipientEmails */
        $recipientEmails = array_values(array_map('strval', $data['recipientEmails'] ?? []));

        return new self(
            recipientEmails: $recipientEmails,
            thirdPartyPayer: isset($data['thirdPartyPayer']) ? (bool) $data['thirdPartyPayer'] : null,
        );
    }
}
