<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\Auth;

use Daika7ana\Dpd\Config\ClientConfig;

final readonly class Credentials
{
    public function __construct(
        public string $username,
        public string $password,
        public string $language = 'RO',
        public ?int $clientSystemId = null,
    ) {}

    public static function fromConfig(ClientConfig $config): self
    {
        return new self(
            username: $config->username,
            password: $config->password,
            language: $config->language,
            clientSystemId: $config->clientSystemId,
        );
    }

    /**
     * @return array<string, int|string>
     */
    public function toRequestPayload(): array
    {
        $payload = [
            'userName' => $this->username,
            'password' => $this->password,
            'language' => $this->language,
        ];

        if ($this->clientSystemId !== null) {
            $payload['clientSystemId'] = $this->clientSystemId;
        }

        return $payload;
    }
}
