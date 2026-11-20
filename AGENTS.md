# AGENTS.md

## Project

Typed PHP 8.3+ SDK for the DPD Romania API (`daika7ana/dpd-php-sdk`, namespace
`Daika7ana\Dpd`). Library only: no server, no web entrypoint, no framework in
`require`. PSR-18 core, one resource class per API area, readonly DTOs, enums.

Base URL `https://api.dpd.ro/v1` (`ClientConfig::BASE_URL_PRODUCTION`); paths are
relative (`/shipment` → `/v1/shipment`). Static credentials go into every JSON
payload — no OAuth exchange.

## Commands

```bash
composer install
composer ci         # validate --strict, pint --test, phpstan, phpunit tests/Unit
composer pint       # style check only, never fixes
composer stan       # PHPStan level 8 over src/
composer test       # full PHPUnit suite, smoke included
composer test:unit  # tests/Unit only
composer test:smoke # tests/Smoke only
vendor/bin/pint     # the only way to auto-fix style
```

- `composer.lock` is gitignored, so CI resolves fresh; keep `guzzlehttp/psr7` at
  `>=2` because `Client::create()` instantiates `GuzzleHttp\Psr7\HttpFactory`.
- Quality gates: PHPStan level 8 over `src/` (vendor/tests excluded), Laravel
  Pint (`per` preset), PHPUnit strict (`.dist`), `composer validate --strict`.
  CI runs validate, Pint and PHPStan on PHP 8.3 and unit tests on 8.3/8.4/8.5,
  branch `master`; smoke only via manual `workflow_dispatch`.

## Layout

`src/` holds `Client.php`, `Config/`, `Auth/`, `Cache/`, `Http/`, `Support/`,
`Exceptions/`, `Enums/`, `DTOs/<Area>/` and `Resources/`. `tests/Support/` holds
the fakes, `tests/Unit/` mirrors `src/`, `tests/Smoke/` the live tests; `docs/`
holds the user guides.

## Conventions

- Never import Symfony, Laravel, or another framework into `src/`, `tests/`, or
  docs, and never make one a runtime dependency; framework integration is out of
  scope. No new runtime dependencies without an explicit decision. Requests are
  built through the injected PSR-17 factories and absolute URIs assembled
  internally; callers never pass a URL.
- DTOs are `final readonly` with `fromArray()`; request DTOs add `toArray()`.
- `docs/` is user-facing documentation: behavior changes go in the matching file,
  not just in code. `CHANGELOG.md` is Keep a Changelog, newest first, tags `vX.Y.Z`.
- Commits and PR titles use Conventional Commits (`<type>(<scope>): <subject>`,
  lowercase imperative, no trailing period).

## HTTP Transport And Timeouts

- `Client` builds requests with the injected PSR-17 `RequestFactoryInterface` /
  `StreamFactoryInterface`, consumes PSR-7 `ResponseInterface`, and depends only
  on PSR-18 `Psr\Http\Client\ClientInterface` — never a concrete client.
- `src/Http/HttpClientInterface.php` widens PSR-18 with
  `sendRequest(RequestInterface $request, ?float $timeout = null)`.
  `src/Http/GuzzleHttpClient.php` is the bundled transport (Guzzle `http_errors`
  and redirects disabled, so 4xx/5xx reach the mappers);
  `src/Http/Psr18HttpClientAdapter.php` wraps any other PSR-18 client, rejects a
  non-null per-call timeout with `InvalidArgumentException`, and rethrows a
  foreign `ClientExceptionInterface` as `TransportException` (original as
  `previous`). `TransportException` implements `ClientExceptionInterface`.
- `Client::__construct()` throws `InvalidArgumentException` when
  `ClientConfig::timeout` is non-null and the client is not this package's
  `HttpClientInterface`. `ClientConfig::timeout` is `?float`, default `null`;
  `connectTimeout` exists only on
  `GuzzleHttpClient::__construct(?GuzzleClient $guzzleClient = null, ?float $timeout = null, ?float $connectTimeout = null)`,
  which applies `DEFAULT_TIMEOUT = 30.0` / `DEFAULT_CONNECT_TIMEOUT = 10.0` only
  when it creates its own Guzzle client. Precedence: per-call
  `Client::send($request, $t)` → `ClientConfig::timeout` → transport constructor
  arguments → 30.0/10.0.
- `guzzlehttp/guzzle` and `guzzlehttp/psr7` are runtime dependencies (default
  transport and `GuzzleHttp\Psr7\HttpFactory`), so `Client::create()` needs no
  arguments; no `class_exists` guard, no `suggest` entry.

## API Quirks

- `ClientConfig` takes `username`, `password`, `language` (upper-cased) and
  `clientSystemId`; `fromEnvironment()` reads `DPD_BASE_URL`, `DPD_USERNAME`,
  `DPD_PASSWORD`, `DPD_LANGUAGE`, `DPD_CLIENT_SYSTEM_ID`, `DPD_TIMEOUT`. Redact
  request logs: those credentials are merged into every JSON payload.
  `setCredentials()` needs a `MutableCredentialsProviderInterface` provider.
- Resource areas: `services()`, `shipments()`, `locations()`, `printing()`,
  `tracking()`, `pickup()`, `validation()`, `calculations()`, `clients()`,
  `payments()`. `ApiRequest::json()`, `::binary()` and `::csv()` set `Accept`;
  binary and CSV calls return `BinaryContent`; `validation()` maps results with
  `throwOnError: false`. Reference data is cached via
  `ReferenceDataCacheInterface` (`Client::rememberReferenceData()`).

## Tests

- Unit tests never touch the network. `TestClientFactory::create()` takes a
  `FakeHttpClient` (built from `callable(RequestInterface): ResponseInterface`)
  plus an optional cache and returns a configured `Client`.
- `FakeHttpClient` records `lastRequest` and `lastTimeout`; assert method, path,
  query, body and headers. Every new endpoint needs such a test covering request
  and response mapping.
- `tests/Smoke` skips itself without `DPD_USERNAME`/`DPD_PASSWORD`; write flows
  need `DPD_SMOKE_ENABLE_WRITES=true`. `composer ci` must pass before done.
