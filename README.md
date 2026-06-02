# DPD PHP SDK

<div align="center">

[![CI](https://github.com/daika7ana/dpd-php-sdk/actions/workflows/ci.yml/badge.svg)](https://github.com/daika7ana/dpd-php-sdk/actions/workflows/ci.yml)
[![PHP 8.3+](https://img.shields.io/badge/PHP-8.3%2B-777BB4?style=flat-square&logo=php)](https://www.php.net/)
[![PHPUnit](https://img.shields.io/badge/PHPUnit-12.x-0A7BBB?style=flat-square)](docs/TESTING.md)
[![PHPStan](https://img.shields.io/badge/PHPStan-2.x-6C43E0?style=flat-square)](docs/TESTING.md)
[![Smoke Tests](https://img.shields.io/badge/Smoke%20Tests-Live%20API-orange?style=flat-square)](docs/TESTING.md)
[![PSR Standards](https://img.shields.io/badge/PSR-7%2F17%2F18-blue?style=flat-square)](https://www.php-fig.org/)
[![License](https://img.shields.io/badge/license-GPL--3.0--or--later-green?style=flat-square)](https://spdx.org/licenses/GPL-3.0-or-later.html)

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
- [Operational Notes](#operational-notes)
- [Testing](#testing)
- [Documentation](#documentation)

## Features

### Core Strengths

- ✅ **Fully Typed DTOs** — Explicit request and response objects across the implemented API surface
- ✅ **Immutable Client Configuration** — Optional environment bootstrapping without mutable runtime defaults
- ✅ **PSR-18 HTTP Client** — Guzzle wired by default with custom client seams when needed
- ✅ **Mixed Response Handling** — Shared JSON, binary, and CSV support for DPD's rough edges
- ✅ **Typed Exception Mapping** — Transport, authentication, validation, and API failures are separated cleanly
- ✅ **Reference-Data Caching** — Optional caching for CSV-backed location imports
- ✅ **Batching Helpers** — Explicit support for capped flows such as batched tracking and bulk hydration

### Good Fit For

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

## Configuration

`ClientConfig` keeps connection settings explicit and immutable:

- `baseUrl`
- `username`
- `password`
- `language`
- `clientSystemId`
- `timeout`
- `connectTimeout`

Use `ClientConfig::fromEnvironment()` when you want configuration to come from environment variables instead of application code.

## Authentication

DPD uses static credentials on every request payload instead of OAuth.

The client injects these values automatically:

- `userName`
- `password`
- `language`
- `clientSystemId` when configured

For multi-tenant or rotating-credential setups, you can inject a custom credentials provider into `Client::create()`.

## Supported Resources

- `services()` for service catalog and destination-aware service discovery
- `shipments()` for create, cancel, update, lookup, barcode, add-parcel, and handover flows
- `locations()` for detail lookups, capped searches, nearest-office search, and CSV-backed reference data
- `printing()` for PDF or ZPL labels, extended print metadata, and vouchers
- `tracking()` for direct tracking, batched tracking, and bulk tracking file discovery
- `pickup()`, `validation()`, `calculations()`, `clients()`, and `payments()` for the remaining operational endpoints

## Operational Notes

- DPD credentials are attached to every request payload, so request logging should always be treated as sensitive.
- Printing endpoints may return binary documents on success and JSON errors on failure. Use `printing()` or `sendBinary()` for those flows.
- Location search endpoints are capped by DPD. Use `find*()` for interactive lookup and `all*()` for full CSV-backed datasets.
- `tracking()->track()` keeps the upstream 10-parcel limit visible. Use `trackBatched()` when you want explicit chunking.
- Non-ASCII address data may still be affected by legacy transliteration rules in DPD label and back-office tooling. Validate with real labels before rollout.

## Testing

### Run The Local Verification Pipeline

```bash
composer ci
```

### Run Smoke Tests Against A Live DPD Account

```bash
composer test:smoke
```

`composer ci` runs formatting, static analysis, and the unit suite. Smoke tests skip automatically when the required environment values are missing.

## Documentation

Complete guides and references:

| Guide | Purpose |
|-------|---------|
| 🔧 [Installation](docs/INSTALLATION.md) | Setup and transport expectations |
| ⚙️ [Configuration](docs/CONFIGURATION.md) | Connection settings and extension points |
| 🔐 [Authentication](docs/AUTHENTICATION.md) | DPD credential flow and provider options |
| 📖 [Quick Start](docs/QUICKSTART.md) | First working client, shipment, print, and tracking flow |
| 🧩 [Examples](docs/EXAMPLES.md) | Small, consistent workflow examples |
| 📚 [Usage](docs/USAGE.md) | Workflow guidance and integration patterns |
| 🔗 [Resources](docs/RESOURCES.md) | Method-level public API reference |
| 📦 [DTOs](docs/DTOS.md) | DTO conventions and domain structure |
| ❌ [Errors](docs/ERRORS.md) | Exception hierarchy and handling |
| ✅ [Testing](docs/TESTING.md) | Unit, smoke, and release verification |

---

<div align="center">

Made with ❤️

**Questions?** [Check the docs](docs/USAGE.md) or [open an issue](../../issues)

</div>
