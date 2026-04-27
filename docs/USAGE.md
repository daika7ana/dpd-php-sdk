# Usage

Use typed resources for normal integration work. Drop to the low-level client helpers only when you are testing an endpoint that does not have a first-class resource yet.

## Typical Flow

1. Build a `ClientConfig` or use `ClientConfig::fromEnvironment()`.
2. Create the client with `Client::create()`.
3. Send typed request DTOs through a resource such as `shipments()` or `locations()`.
4. Catch `DpdException` subclasses at the edge of your integration.

## Choose The Right Resource

- `services()` for catalog and destination-aware service discovery
- `shipments()` for create, cancel, update, lookup, barcode, add-parcel, and handover flows
- `locations()` for detail lookups, capped searches, nearest-office search, and bulk reference data
- `printing()` for PDF or ZPL labels, extended metadata, and vouchers
- `tracking()` for direct tracking, batched tracking, and bulk tracking files
- `pickup()`, `validation()`, `calculations()`, `clients()`, and `payments()` for the remaining operational endpoints

## Shipment Flows

Prefer the typed shipment graph over ad hoc arrays so address variants, payment fields, and additional services stay explicit.

- use `LocalShipmentAddress` for domestic address type `1` payloads
- use `ForeignShipmentAddress` for address type `2` payloads
- reuse shared DTOs such as `AddressLocation`, `ShipmentPhoneNumber`, and `ShipmentParcelRef`
- use `updateProperties()` only for targeted field updates; the request validates supported shipment property paths before the payload is sent

```php
$shipment = $client->shipments()->create($shipmentRequest);
$details = $client->shipments()->info($shipmentInformationRequest);
```

The resource exposes both cancel variants documented by DPD: `cancel()` for the POST `/shipment/cancel` flow and `cancelWithDelete()` for the DELETE `/shipment` flow.

## Location Flows

DPD exposes two different location access patterns and the SDK keeps the split visible:

- `find*()` methods are capped search endpoints for interactive lookup
- `all*()` methods are CSV-backed imports for full reference datasets

If you configure a `ReferenceDataCacheInterface`, the `all*()` methods can reuse hydrated collections through `rememberReferenceData()` and avoid repeated CSV downloads.

## Printing And Tracking

Printing is mixed-mode at the API level: success can be binary while failure is still JSON. The `printing()` resource keeps that switch in one place and returns typed document objects or mapped exceptions.

- use `printing()->document()` for PDF or ZPL output
- use `printing()->extended()` when label metadata is also needed
- use `tracking()->track()` when the request already fits within DPD's 10-parcel limit
- use `tracking()->trackBatched()` when you want the SDK to chunk larger lists and merge the typed result

Tracked operations also expose resolver helpers for operation codes and exception codes so integration logic does not need to duplicate DPD appendix tables.

## Validation Before Writes

Validation and calculation are the safest pre-flight calls when an account is new, address quality is inconsistent, or a shipment payload is assembled from multiple sources.

```php
$addressValidation = $client->validation()->address($localAddress);
$shipmentValidation = $client->validation()->shipment($shipmentRequest);
$calculationGroups = $client->calculations()->calculate($calculationRequest);
```

## Low-Level Helpers

The client still exposes lower-level helpers for endpoints that have not been promoted into a dedicated resource yet:

- `sendJson()`
- `sendBinary()`
- `sendCsv()`

Those helpers still go through the shared credentials pipeline and exception mapping.
