<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Common\Errors;

final readonly class DpdError
{
    /**
     * @param array<string, mixed> $raw
     */
    public function __construct(
        public int $code,
        public string $message,
        public string $id,
        public ?string $context,
        public ?string $component,
        public array $raw,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            code: (int) ($data['code'] ?? 0),
            message: (string) ($data['message'] ?? 'DPD API error.'),
            id: (string) ($data['id'] ?? ''),
            context: isset($data['context']) ? (string) $data['context'] : null,
            component: isset($data['component']) ? (string) $data['component'] : null,
            raw: $data,
        );
    }

    public function isValidationRelated(): bool
    {
        return in_array($this->code, [
            100,
            120,
            160,
            180,
            300,
            400,
            410,
            415,
            420,
            425,
            430,
            435,
            440,
            445,
            450,
            455,
            460,
            500,
            510,
            515,
            520,
            600,
            605,
            610,
            615,
            620,
            630,
            700,
            710,
            800,
            805,
            810,
        ], true);
    }

    public function isAuthenticationRelated(): bool
    {
        $message = strtolower($this->message);

        return str_contains($message, 'auth')
            || str_contains($message, 'password')
            || str_contains($message, 'credential')
            || str_contains($message, 'logged user')
            || str_contains($message, 'access rights');
    }
}
