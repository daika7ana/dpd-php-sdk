# Configuration

`ClientConfig` is immutable and keeps all connection-level settings in one place.

## Core Settings

- `baseUrl`: defaults to the DPD Romania v1 API base URL
- `username`: DPD API username
- `password`: DPD API password
- `language`: request language sent to DPD
- `clientSystemId`: optional client system identifier
- `timeout`: request timeout in seconds
- `connectTimeout`: connection timeout in seconds

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
        connectTimeout: 5.0,
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

## Extension Points

- inject a custom `CredentialsProviderInterface` when credentials come from a tenant resolver, vault, or rotating source
- inject a custom `ReferenceDataCacheInterface` when bulk location imports should be cached between runs
- inject your own PSR-18 client or PSR-17 factories when the host application already provides them

Reference-data caching matters mainly for `locations()->all*()` CSV exports. Search and detail endpoints remain uncached unless you add that behavior yourself.
