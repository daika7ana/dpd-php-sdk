<div align="center">

<h1>DPD PHP SDK</h1>

[![CI](https://github.com/daika7ana/dpd-php-sdk/actions/workflows/ci.yml/badge.svg)](https://github.com/daika7ana/dpd-php-sdk/actions/workflows/ci.yml)
[![PHP 8.3+](https://img.shields.io/badge/PHP-8.3%2B-777BB4?style=flat-square&logo=php)](https://www.php.net/)
[![PHPUnit](https://img.shields.io/badge/PHPUnit-12%E2%80%9313-0A7BBB?style=flat-square)](docs/TESTING.md)
[![Smoke Tests](https://img.shields.io/badge/Smoke%20Tests-Live%20API-orange?style=flat-square)](docs/TESTING.md)
[![PSR Standards](https://img.shields.io/badge/PSR-7%2F17%2F18-blue?style=flat-square)](https://www.php-fig.org/)
[![License](https://img.shields.io/badge/license-GPL--3.0--or--later-green?style=flat-square)](LICENSE)

A modern, type-safe PHP SDK for the **DPD Romania API**

</div>

> 🚀 Framework-agnostic, DPD-credential-based, fully typed with DTOs, and built for production integrations.

## Table of Contents

- [Features](#features)
- [Requirements](#requirements)
- [Installation](#installation)
- [Quick Start](#quick-start)
- [Configuration](#configuration)
- [Authentication](#authentication)
- [Supported Resources](#supported-resources)
- [Framework Support](#framework-support)
- [Testing](#testing)
- [Documentation](#documentation)

## Features

### Core Strengths

- ✅ **Fully Typed DTOs** — Explicit request and response objects across the implemented API surface
- ✅ **Immutable Client Configuration** — Optional environment bootstrapping without mutable runtime defaults
- ✅ **PSR-18 HTTP Client** — PSR-18 transport seams; Guzzle is wired automatically by default
- ✅ **Static Credentials** — DPD username, password, and language are merged into every JSON payload
- ✅ **Ten Resource Areas** — `services()`, `shipments()`, `locations()`, `printing()`, `tracking()`, `pickup()`, `validation()`, `calculations()`, `clients()`, and `payments()`
- ✅ **Mixed Response Handling** — Shared JSON, binary, and CSV support for DPD's rough edges
- ✅ **Typed Exception Mapping** — Transport, authentication, validation, and API failures are separated cleanly
- ✅ **Reference-Data Caching** — Optional caching for CSV-backed location imports
- ✅ **Batching Helpers** — Explicit support for capped flows such as batched tracking and bulk hydration
- ✅ **Static Analysis with PHPStan** — Level 8 checks across the `src/` codebase
- ✅ **Live Smoke Gate** — Manual CI workflow run against a real DPD account

### Perfect For

- 🎯 Laravel applications or any PHP framework
- 🎯 Standalone PHP services and worker processes
- 🎯 Integrations that need explicit shipment and location payloads instead of raw arrays
- 🎯 Teams that want predictable local verification through unit tests, static analysis, and smoke checks

## Requirements

- **PHP 8.3+**
- PSR-7, PSR-17, and PSR-18 compatible environment

## Installation

### Via Composer

```bash
composer require daika7ana/dpd-php-sdk
```

## Quick Start

```php
use Daika7ana\Dpd\Client;
use Daika7ana\Dpd\Config\ClientConfig;

$client = Client::create(
    config: new ClientConfig(
        username: 'demo-user',
        password: 'demo-password',
        language: 'RO',
    ),
);

$services = $client->services()->list();

foreach ($services as $service) {
    echo $service->nameEn . PHP_EOL;
}
```

For a full end-to-end walkthrough, continue with [docs/QUICKSTART.md](docs/QUICKSTART.md) or the shorter [docs/EXAMPLES.md](docs/EXAMPLES.md).

That's it! You've got a configured client and a typed service catalog to work with.

## Configuration

### Base URL

`ClientConfig` keeps connection settings explicit and immutable:

- `baseUrl`
- `username`
- `password`
- `language`
- `clientSystemId`
- `timeout` (optional request timeout in seconds)

`baseUrl` defaults to `https://api.dpd.ro/v1` (`ClientConfig::BASE_URL_PRODUCTION`); resource paths are relative, so `/shipment` resolves to `/v1/shipment`.

### Credentials & Environment Variables

Use `ClientConfig::fromEnvironment()` when you want configuration to come from environment variables instead of application code. It reads:

- `DPD_BASE_URL`
- `DPD_USERNAME`
- `DPD_PASSWORD`
- `DPD_LANGUAGE`
- `DPD_CLIENT_SYSTEM_ID`
- `DPD_TIMEOUT`

### Timeouts

`ClientConfig::timeout` is `null` by default. Precedence: the per-call argument → `ClientConfig::timeout` → the transport's own default (`GuzzleHttpClient` uses 30 s to respond and 10 s to connect unless you pass `timeout:`/`connectTimeout:`; an injected `GuzzleClient` keeps its own configuration). A bare PSR-18 client keeps its own timeout and cannot receive the per-call argument — passing one throws `InvalidArgumentException`; combining a bare client with a non-null `ClientConfig::timeout` also throws.

Connect timeout lives on the transport: `new GuzzleHttpClient(timeout: 30.0, connectTimeout: 5.0)`.

For a different budget, `$slow = $client->withTimeout(30.0);` returns a new
client sharing the transport; the original keeps its timeout.

### Operational Notes

- DPD credentials are attached to every request payload, so request logging should always be treated as sensitive.
- Printing endpoints may return binary documents on success and JSON errors on failure. Use `printing()` or `sendBinary()` for those flows.
- Location search endpoints are capped by DPD. Use `find*()` for interactive lookup and `all*()` for full CSV-backed datasets.
- `tracking()->track()` keeps the upstream 10-parcel limit visible. Use `trackBatched()` when you want explicit chunking.
- Non-ASCII address data may still be affected by legacy transliteration rules in DPD label and back-office tooling. Validate with real labels before rollout.

## Authentication

DPD uses static credentials on every request payload instead of OAuth.

The client injects these values automatically:

- `userName`
- `password`
- `language`
- `clientSystemId` when configured

Credentials come from `ClientConfig` or, through `ClientConfig::fromEnvironment()`, from the `DPD_USERNAME`, `DPD_PASSWORD`, `DPD_LANGUAGE`, and `DPD_CLIENT_SYSTEM_ID` environment variables.

For multi-tenant or rotating-credential setups, you can inject a custom credentials provider into `Client::create()`. If that provider is mutable, `Client::setCredentials()` can update credentials at runtime.

## Supported Resources

### Services, Shipments, Locations, Printing & Tracking

- `services()` for service catalog and destination-aware service discovery
- `shipments()` for create, cancel, update, lookup, barcode, add-parcel, and handover flows
- `locations()` for detail lookups, capped searches, nearest-office search, and CSV-backed reference data
- `printing()` for PDF or ZPL labels, extended print metadata, and vouchers
- `tracking()` for direct tracking, batched tracking, and bulk tracking file discovery

### Additional Operations

- `pickup()` for pickup ordering and terms
- `validation()` for address, post code, phone, and shipment validation
- `calculations()` for shipment price calculation
- `clients()` for client, contract, and contact lookups
- `payments()` for payout data

## Framework Support

### Zero Framework Dependencies

This package works equally well everywhere:

- 🌐 **Laravel** applications
- 🌐 **Symfony** projects
- 🌐 **Standalone PHP** applications
- 🌐 **Microservices**
- 🌐 Any **PHP 8.3+** environment

### Custom HTTP Client

The SDK ships with a Guzzle-backed PSR-18 transport, but any PSR-18 client works (auto-wrapped):

```php
use Daika7ana\Dpd\Client;
use GuzzleHttp\Psr7\HttpFactory;

$factory = new HttpFactory();

/** @var \Psr\Http\Client\ClientInterface $psr18 */
$client = Client::create(
    httpClient: $psr18,
    requestFactory: $factory,
    streamFactory: $factory,
);
```

A bare PSR-18 client keeps its own timeout and cannot receive the per-call timeout argument — passing one throws `InvalidArgumentException`; combining a bare client with a non-null `ClientConfig::timeout` also throws.

## Testing

### Run Static Analysis

```bash
composer stan
# or
vendor/bin/phpstan --no-progress
```

PHPStan is configured via `phpstan.neon` and analyzes the `src/` directory at level 8.

### Run Full Test Suite

```bash
composer test
# or
php vendor/bin/phpunit
```

Run a single suite with `composer test:unit` (`tests/Unit`) or `composer test:smoke` (`tests/Smoke`).

### Run Smoke Tests Against A Live DPD Account

```bash
composer test:smoke
```

Smoke tests skip automatically when the required environment values are missing.

### Run The Same Checks As CI

```bash
composer ci
```

`composer ci` validates `composer.json`, then runs formatting, static analysis, and the unit suite. Available Composer scripts:

- `composer pint` runs Pint in test mode
- `composer stan` runs PHPStan against `src/`
- `composer test` runs the full PHPUnit suite
- `composer test:unit` runs the unit test suite
- `composer test:smoke` runs the smoke test suite
- `composer ci` runs the same local checks as CI

### Test Environment Variables

Configure these in `phpunit.xml` to enable smoke tests:

| Variable | Purpose |
|----------|---------|
| `DPD_BASE_URL` | API base URL override |
| `DPD_USERNAME` | DPD API username |
| `DPD_PASSWORD` | DPD API password |
| `DPD_LANGUAGE` | Request language |
| `DPD_CLIENT_SYSTEM_ID` | Optional client system identifier |
| `DPD_SMOKE_ENABLE_WRITES` | Set to `true` to enable shipment creation and pickup ordering |

Optional fixture overrides such as `DPD_SMOKE_COUNTRY_ID`, `DPD_SMOKE_SITE_ID`, and `DPD_SMOKE_TRACK_PARCEL_ID` are documented in [docs/TESTING.md](docs/TESTING.md).

[See detailed testing docs →](docs/TESTING.md)

## Documentation

Complete guides and references:

| Guide | Purpose |
|-------|---------|
| 📖 [Quickstart](docs/QUICKSTART.md) | First working client, shipment, print, and tracking flow; also [small workflow examples](docs/EXAMPLES.md) |
| 🔧 [Installation](docs/INSTALLATION.md) | Setup and transport expectations |
| ⚙️ [Configuration](docs/CONFIGURATION.md) | Connection settings and extension points |
| 🔐 [Authentication](docs/AUTHENTICATION.md) | DPD credential flow and provider options |
| 🔗 [Resources](docs/RESOURCES.md) | Method-level public API reference |
| 📦 [DTOs](docs/DTOS.md) | DTO conventions and domain structure |
| ✅ [Testing](docs/TESTING.md) | Unit, smoke, and release verification |
| ❌ [Errors](docs/ERRORS.md) | Exception hierarchy and handling |
| 📚 [All Docs](docs/USAGE.md) | Workflow guidance and integration patterns |

---

<div align="center">

Made with ❤️

**Questions?** [Check the docs](docs/USAGE.md) or [open an issue](../../issues)

</div>
