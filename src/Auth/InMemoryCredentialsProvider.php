<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\Auth;

use Daika7ana\Dpd\Config\ClientConfig;

final class InMemoryCredentialsProvider implements MutableCredentialsProviderInterface
{
    public function __construct(
        private Credentials $credentials,
    ) {}

    public static function fromConfig(ClientConfig $config): self
    {
        return new self(Credentials::fromConfig($config));
    }

    public function getCredentials(): Credentials
    {
        return $this->credentials;
    }

    public function setCredentials(Credentials $credentials): void
    {
        $this->credentials = $credentials;
    }
}
