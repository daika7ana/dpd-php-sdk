# DTOs

The SDK is DTO-first. Request payloads, response payloads, shared value objects, and enums are all explicit so integrations can stay out of raw nested arrays.

## Conventions

- DTOs are `final readonly` where practical
- hydration is usually done through `fromArray()` factories
- enums are used for constrained API values
- shared primitives are reused across resources instead of duplicating shapes

## Shared Building Blocks

Common DTOs cover addresses, locations, phone numbers, parcel references, parcel sizes, money values, label metadata, routing data, bank accounts, and tracking resolver payloads.

That shared layer keeps services, shipments, locations, printing, and tracking aligned on the same value objects.

## Shipment DTOs

The shipment graph is the largest part of the package and includes:

- typed request DTOs for create, cancel, finalize, info, update, add-parcel, search, handover, barcode, and secondary-shipment flows
- explicit address variants through `LocalShipmentAddress` and `ForeignShipmentAddress`
- dedicated additional-service DTOs for COD, declared value, returns, OBPD, fixed-time, special delivery, delivery-to-floor, and return-voucher flows
- typed response models for shipment creation, parcel records, payment records, barcode information, and secondary shipments

`UpdateShipmentPropertiesRequest` is intentionally narrower than a raw key-value map. It normalizes supported property-path syntax and rejects unsupported shipment paths before the request is sent.

## Location DTOs

Location and reference-data DTOs cover countries, states, sites, streets, complexes, blocks, points of interest, postcodes, offices, office schedules, and nearest-office results.

The same DTO families are reused for both JSON search responses and CSV-backed bulk imports so callers do not need two different hydration models for the same data.

## Tracking And Print DTOs

Tracking models preserve parcel history, operation metadata, exception details, and batch responses. Print models keep request intent and document metadata explicit for PDF and ZPL flows.

If you are looking for the public entry points that use these DTOs, continue with [Resources](RESOURCES.md).
