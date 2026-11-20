# Configuration

`ClientConfig` is immutable and keeps all connection-level settings in one place.

## Core Settings

- `baseUrl`: defaults to the DPD Romania v1 API base URL
- `username`: DPD API username
- `password`: DPD API password
- `language`: request language sent to DPD
- `clientSystemId`: optional client system identifier
- `timeout`: optional request timeout in seconds; `null` by default

`ClientConfig::timeout` is `null` by default. Precedence: the per-call argument → `ClientConfig::timeout` → the transport's own default (`GuzzleHttpClient` uses 30 s to respond and 10 s to connect unless you pass `timeout:`/`connectTimeout:`; an injected `GuzzleClient` keeps its own configuration). A bare PSR-18 client keeps its own timeout and cannot receive the per-call argument — passing one throws `InvalidArgumentException`; combining a bare client with a non-null `ClientConfig::timeout` also throws.

Connect timeout now lives on the transport: `new GuzzleHttpClient(timeout: 30.0, connectTimeout: 5.0)`.

## Build A Client Explicitly

```php
use Daika7ana\Dpd\Client;
use Daika7ana\Dpd\Config\ClientConfig;

$client = Client::create(
    config: new ClientConfig(
        username: 'demo-user',
        password: 'demo-password',
        language: 'EN',
        clientSystemId: 123,
        timeout: 10.0,
    ),
);
```

## Build From Environment

Use `ClientConfig::fromEnvironment()` when you want bootstrap to stay outside application code.

Supported environment variables:

- `DPD_BASE_URL`
- `DPD_USERNAME`
- `DPD_PASSWORD`
- `DPD_LANGUAGE`
- `DPD_CLIENT_SYSTEM_ID`
- `DPD_TIMEOUT`

## Extension Points

- inject a custom `CredentialsProviderInterface` when credentials come from a tenant resolver, vault, or rotating source
- inject a custom `ReferenceDataCacheInterface` when bulk location imports should be cached between runs
- inject your own PSR-18 client or PSR-17 factories when the host application already provides them

Reference-data caching matters mainly for `locations()->all*()` CSV exports. Search and detail endpoints remain uncached unless you add that behavior yourself.
