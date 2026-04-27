<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Common\Addressing;

final readonly class BankAccount
{
    public function __construct(
        public string $iban,
        public string $accountHolder,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            iban: (string) ($data['iban'] ?? ''),
            accountHolder: (string) ($data['accountHolder'] ?? ''),
        );
    }

    /**
     * @return array{iban: string, accountHolder: string}
     */
    public function toArray(): array
    {
        return [
            'iban' => $this->iban,
            'accountHolder' => $this->accountHolder,
        ];
    }
}
