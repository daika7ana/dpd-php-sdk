# Testing

The package has three verification layers: unit tests, static analysis and formatting, plus optional smoke tests against a live DPD account.

## Local Commands

Run the unit suite:

```bash
composer test:unit
```

Run the full local verification pipeline:

```bash
composer ci
```

Run smoke tests when live DPD credentials are configured:

```bash
composer test:smoke
```

Run static analysis directly:

```bash
composer stan
```

Run formatting directly:

```bash
vendor/bin/pint --dirty --format agent
```

## Smoke Test Environment

Smoke tests read the same core credentials as `ClientConfig::fromEnvironment()`:

- `DPD_BASE_URL`
- `DPD_USERNAME`
- `DPD_PASSWORD`
- `DPD_LANGUAGE`
- `DPD_CLIENT_SYSTEM_ID`

Built-in smoke fixtures now cover the common address, recipient, and service fields automatically. By default the suite resolves a real Romanian site and street from the live API and uses realistic fake recipient data.

Optional fixture overrides:

- `DPD_SMOKE_COUNTRY_ID`
- `DPD_SMOKE_SITE_ID`
- `DPD_SMOKE_SITE_NAME`
- `DPD_SMOKE_POST_CODE`
- `DPD_SMOKE_STREET_TYPE`
- `DPD_SMOKE_STREET_NAME`
- `DPD_SMOKE_STREET_NO`
- `DPD_SMOKE_SERVICE_ID`
- `DPD_SMOKE_RECIPIENT_NAME`
- `DPD_SMOKE_RECIPIENT_PHONE`
- `DPD_SMOKE_RECIPIENT_EMAIL`
- `DPD_SMOKE_TRACK_PARCEL_ID`
- `DPD_SMOKE_PRINT_PARCEL_ID`

Optional smoke controls:

- `DPD_SMOKE_ENABLE_WRITES=1` enables shipment creation and pickup ordering
- `DPD_SMOKE_CANCEL_CREATED_SHIPMENTS=1` cancels shipments created by the smoke suite
- `DPD_SMOKE_PICKUP_SHIPMENT_ID` reuses an existing shipment for pickup smoke
- `DPD_SMOKE_EXPECT_PAYOUTS=1` requires payout smoke to return data instead of only type-safe structure
- `DPD_SMOKE_PRINT_FORMAT=zpl` switches print smoke from PDF to ZPL

When `DPD_SMOKE_ENABLE_WRITES=1` is enabled, tracking and printing smoke tests can self-seed from a newly created shipment if no parcel reference env vars are configured.

Smoke tests skip automatically when required credentials are missing, when account-specific parcel references are not configured, or when the configured DPD account cannot resolve the default live fixtures.

## What Is Covered

- client bootstrapping and extension seams
- response and exception mapping
- shipment request and resource behavior
- location search, lookup, nearest-office, CSV hydration, and cache-backed reference data
- DTO edge cases such as domestic versus foreign address payloads and optional nested structures
- live smoke workflows for services, validation, calculations, tracking, printing, shipments, pickup, and payouts when the account supports them

## Release Check

Before tagging a release, run:

```bash
composer ci
composer test:smoke
```

Smoke support is contract-aware. Record which workflows are available for the release account instead of assuming every DPD account exposes the same feature set.
