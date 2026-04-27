<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\Auth;

interface CredentialsProviderInterface
{
    public function getCredentials(): Credentials;
}
