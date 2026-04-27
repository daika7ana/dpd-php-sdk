<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\Auth;

interface MutableCredentialsProviderInterface extends CredentialsProviderInterface
{
    public function setCredentials(Credentials $credentials): void;
}
