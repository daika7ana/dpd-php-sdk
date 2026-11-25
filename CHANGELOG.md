# Changelog

All notable changes to this project are documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [3.4.0] - 2026-11-25

### Changed

- **Breaking:** `ValidationException` gains an optional `$errors` parameter as its first constructor argument and defaults the message to `Validation failed`.
- `TestClientFactory::create()` takes a request handler, an optional `ClientConfig` and an optional `?ReferenceDataCacheInterface $cache = null` third parameter, and returns `[Client, FakeHttpClient]` like the sibling SDKs; test support only, not shipped to consumers.
- `GuzzleHttpClient` is no longer `final`.
- `TransportException` defaults its message to `Transport error`.

## [3.3.0] - 2026-11-23

### Added

- Live smoke coverage for the `clients()` resource (`ownClientId`, `get`, `contractClients`, `contractInfo`).

### Changed

- **Breaking:** `DpdException` is now a plain marker extending `Exception`; `$response`, `$retryAfter`, `$error` and `$responseDetails` moved to the concrete HTTP exceptions.
- **Breaking:** `AuthenticationException`, `ValidationException` and `UnexpectedStatusException` expose `$response`, `$retryAfter` and `$error`; `UnexpectedStatusException` also exposes `$responseDetails`.
- **Breaking:** `ValidationException` now extends `DpdException` directly.
- **Breaking:** HTTP exceptions now report the HTTP status from `getCode()` (or `0` without a response); carrier error codes stay on `$error->code`.
- **Breaking:** exception classification is status-only: `400`/`422` raise `ValidationException`, `401` raises `AuthenticationException`, and every other non-2xx status raises `UnexpectedStatusException`; payload predicates are advisory hints only.
- `DpdError::fromArray()` accepts a `$fallbackCode` used when the payload omits `code`, and `isAuthenticationRelated()` recognizes codes `401` and `403`.
- Require `ext-json` and add the package `homepage` and `support` metadata.

### Removed

- **Breaking:** removed `ApiException` and `UnsupportedResponseException`; `UnexpectedStatusException` now handles non-2xx statuses and unexpected response shapes.

### Fixed

- Correct the authentication guide: only an HTTP `401` raises `AuthenticationException`; a `403` raises `UnexpectedStatusException`.

## [3.2.0] - 2026-11-22

### Added

- `Client::withTimeout(?float $timeout)` derives a client sharing the transport, PSR-17 factories, credentials provider and reference-data cache with a different request timeout budget — cached reference data is not refetched and no new transport is built.
- HTTP-level exceptions thrown by `ApiResponseMapper` (`ApiException`, `AuthenticationException`, `UnsupportedResponseException`, `ValidationException`) expose the raw PSR-7 `ResponseInterface` as `$exception->response` and the numeric `Retry-After` value in seconds as `$exception->retryAfter` (`null` when absent or non-numeric); both are optional properties on `DpdException`.

## [3.1.0] - 2026-11-20

### Added

- `Client::create()` and `Client::__construct()` accept any PSR-18 `ClientInterface`; a bare client is wrapped in `Http\Psr18HttpClientAdapter`, so no adapter is needed.
- `GuzzleHttpClient::DEFAULT_TIMEOUT` (30 s) and `DEFAULT_CONNECT_TIMEOUT` (10 s), plus `timeout:` and `connectTimeout:` constructor arguments for the Guzzle client it builds.
- `TransportException` implements `Psr\Http\Client\ClientExceptionInterface`.

### Changed

- **Breaking:** `ClientConfig::timeout` is now `?float` and defaults to `null`. `ClientConfig::DEFAULT_TIMEOUT`, `ClientConfig::DEFAULT_CONNECT_TIMEOUT`, `ClientConfig::$connectTimeout` and the `DPD_CONNECT_TIMEOUT` environment variable are removed; set a connect timeout with `new GuzzleHttpClient(connectTimeout: 5.0)`.
- **Breaking:** `ClientConfig::withTimeouts()` is now `withTimeout(?float $timeout)`; passing `null` clears the timeout.
- Timeouts are applied per request. Precedence: the `Client::send()` argument, then `ClientConfig::timeout`, then the transport defaults (30 s request / 10 s connect when `GuzzleHttpClient` builds its own client). A non-null `ClientConfig::timeout` combined with a foreign PSR-18 client throws `InvalidArgumentException`, and a per-call timeout passed to such a client throws too.
- A PSR-18 client that throws a `ClientExceptionInterface` is now wrapped in `TransportException`.

### Fixed

- `GuzzleHttpClient` now sends `http_errors => false` and `allow_redirects => false` on the per-request timeout path as well, so 4xx/5xx responses reach `ApiResponseMapper`. On that path they now raise `ApiException`/`ValidationException`/`AuthenticationException` instead of `TransportException`; all of them extend `DpdException`.

## [3.0.0] - 2026-11-19

### Changed

- **Breaking:** collections are now `Daika7ana\SimpleCollections\Collection`, pulled in automatically as a runtime dependency ([`daika7ana/simple-collections`](https://packagist.org/packages/daika7ana/simple-collections) `^1.0`), replacing the deleted in-tree helper. Update imports:

  ```diff
  - use Daika7ana\Dpd\DTOs\Common\Support\Collection;
  + use Daika7ana\SimpleCollections\Collection;
  ```

  Method names, key-preserving `pluck()`, `ArrayAccess` and iteration for int/string offsets are unchanged. One signature detail: `pluck()`'s first parameter is now `$value` (was `$valueKey`), so named-argument calls must be updated.
- Collections returned inside DTOs are mutable: the package adds `push()`, `put()`, `forget()`, and other mutators. `final readonly` DTOs no longer make their collection members immutable, so clone the collection before modifying it when you reuse a response object.
- **Breaking:** collection members now encode like their underlying PHP array. The package `Collection` implements `JsonSerializable` and returns its items from `jsonSerialize()`, so `json_encode()` on a DTO no longer emits `{}` for those members: list-keyed collections become JSON arrays, string-keyed collections (for example after `keyBy()`) become JSON objects, and an empty collection becomes `[]`. Update snapshots that asserted `{}`.

### Removed

- **Breaking:** Removed `Daika7ana\Dpd\DTOs\Common\Support\Collection`; subclassing or type-hinting it now fails — use `Daika7ana\SimpleCollections\Collection`.

## [2.0.0] - 2026-11-19

### Added

- `OfficeSearchRequest::$officeType` and `$officeFeatures` filters for `/location/office`.
- `ValidationResult::$error`, exposing the API error when validation returns `valid: false`.
- Tracking operation codes `120` (Refusal to send) and `152` (Routed to another DPD Location).

### Changed

- `CalculationsResource::calculate()` returns a flat `Collection<int, CalculationResult>` matching the live `/calculate` response instead of list-of-lists.
- `ValidatePostCodeRequest` throws `InvalidArgumentException` when neither `countryId` nor `siteId` is given.
- `Client::sendJson()` accepts `$throwOnError`; validation endpoints use it so invalid input is returned, not thrown.

### Fixed

- Shipment and midway-carrier handover datetimes use the `+0200` offset format the API requires (`Y-m-d\TH:i:sO`); `+02:00` was rejected with HTTP 400.
- Non-JSON 4xx and 5xx responses map to `ApiException` instead of `UnsupportedResponseException`.

## [1.1.0] - 2026-10-27

### Added

- `Client::send()` accepts an optional per-request timeout that overrides the configured one.
- `ShipmentSender::$objectName`, matching the field the API already accepts.
- `ClientConfig::DEFAULT_TIMEOUT` and `ClientConfig::DEFAULT_CONNECT_TIMEOUT`.
- Unit-test factory `Tests\Support\TestClientFactory`, `FakeHttpClient::$lastTimeout`, and a manual `workflow_dispatch` live-smoke CI job.

### Changed

- Timeouts are on by default: `timeout` 30 s, `connectTimeout` 10 s. `ClientConfig::$timeout` and `$connectTimeout` are now non-nullable floats; `withTimeouts()` keeps the current value for arguments passed as `null`.
- Non-numeric `DPD_TIMEOUT` / `DPD_CONNECT_TIMEOUT` values fall back to the defaults instead of disabling the timeout.
- `HttpClientInterface::sendRequest()` accepts an optional timeout. Custom implementations must add the parameter.
- Response DTOs map enums with `tryFrom()`: unknown money, shipment, office and address types keep the field's existing default (for example `Cash` or `ReturnShipment`) instead of throwing `ValueError`. Neutral capability flags keep a conservative default.
- Optional empty lists are omitted from request payloads instead of being sent as `[]`.
- `ShipmentContent` and `CalculationContent` throw `InvalidArgumentException` when there are no parcels and no `parcelsCount`/`totalWeight`.
- `pickupDateTime` keeps the caller's timezone offset (no UTC conversion); `visitEndTime` rejects impossible times such as `99:99`.
- `track()` and `trackBatched()` enforce the API limit of 10 parcels per request.
- Binary printing accepts `application/octet-stream` and detects PDF by its `%PDF-` signature.
- Reference CSV rows are streamed while mapping; the cache stores a plain array, so each call returns a fresh `Collection`.
- `ArrayReferenceDataCache` keeps at most 50 entries and prunes expired ones on insert.
- `ApiResponseMapper` no longer copies the raw response body or payload into `responseDetails`; only status, content type and error code/message are kept.
- `JsonHelper::decode()` rejects valid JSON that is not an object or array (`null`, scalars) with `JsonException`.
- `guzzlehttp/psr7` is now a declared dependency. Supported ranges are Guzzle `^7.0 || ^8.0`, psr7 `^2.0 || ^3.0`, and PHPUnit `^12.0 || ^13.0` (verified on Guzzle 8.3.1, psr7 3.2.0 and PHPUnit 13.4.5).
- Composer `pint` runs in `--test` mode, so `composer ci` and the CI style job can fail.
- CI adds a manual live-smoke job (`run_smoke`, `allow_writes` inputs).

### Fixed

- HTTP 401/403 responses map to `AuthenticationException`, including non-JSON bodies and auth messages that do not contain English keywords.
- Malformed, scalar or `null` JSON bodies map to `UnsupportedResponseException` instead of a raw `JsonException` or `TypeError`.
- `204`/`205` responses decode to an empty array instead of failing; an empty `200` body still raises `UnsupportedResponseException`.
- An empty or non-CSV reference data response (for example an HTML error page) throws instead of caching an empty or garbage collection.
- Missing `clientId` throws `UnsupportedResponseException` instead of returning `0`; a tracking response with a non-array `parcels` value throws, while a missing `parcels` key is treated as an empty result.
- HTTP 401/403 responses with a JSON body that has no `error` key map to `AuthenticationException`, including on binary endpoints.
- A ZPL label served as `application/octet-stream` is detected as ZPL.
- `docs/ERRORS.md` uses the real `$exception->error` property.
