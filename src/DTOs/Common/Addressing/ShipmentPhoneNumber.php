<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Common\Addressing;

final readonly class ShipmentPhoneNumber
{
    public function __construct(
        public string $number,
        public ?string $extension = null,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            number: (string) ($data['number'] ?? ''),
            extension: isset($data['extension']) ? (string) $data['extension'] : (isset($data['ext']) ? (string) $data['ext'] : null),
        );
    }

    /**
     * @return array{number: string, extension?: string}
     */
    public function toArray(): array
    {
        return array_filter([
            'number' => $this->number,
            'extension' => $this->extension,
        ], static fn(mixed $value): bool => $value !== null);
    }
}
