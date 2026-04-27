# Authentication

DPD uses static credentials on every request payload instead of OAuth-style token exchange.

## What The SDK Adds Automatically

Every JSON request is enriched with:

- `userName`
- `password`
- `language`
- `clientSystemId` when configured

You do not need to add those fields manually to request DTOs.

## Default Behavior

`Client::create()` uses an in-memory credentials provider built from `ClientConfig`.

That is enough for most single-account integrations.

## Custom Credential Sources

Inject a custom `CredentialsProviderInterface` when credentials need to come from:

- a secrets manager
- a tenant-aware credential resolver
- a mutable runtime store

If the configured provider is mutable, `Client::setCredentials()` can update credentials at runtime.

## Failure Handling

Credential-related failures are mapped to `AuthenticationException`.

Because credentials are copied into every request payload, raw request logging should always be redacted.
