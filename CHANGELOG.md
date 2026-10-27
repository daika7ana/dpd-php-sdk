# Changelog

All notable changes to this project are documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

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
