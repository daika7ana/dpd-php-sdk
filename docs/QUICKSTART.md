# Quick Start

This guide walks through the shortest useful path: build a client, inspect services, create a shipment, then print or track it.

## 1. Build The Client

```php
use Daika7ana\Dpd\Client;
use Daika7ana\Dpd\Config\ClientConfig;

$client = Client::create(
    config: new ClientConfig(
        username: 'demo-user',
        password: 'demo-password',
        language: 'EN',
    ),
);
```

The client injects DPD credentials into each request payload automatically.

## 2. Read Services And Reference Data

```php
$services = $client->services()->list();
$countries = $client->locations()->allCountries(ttlSeconds: 3600);

foreach ($services as $service) {
    echo $service->nameEn . PHP_EOL;
}

foreach ($countries as $country) {
    echo $country->name->english . PHP_EOL;
}
```

Use `locations()->find*()` for capped interactive lookups and `locations()->all*()` for full CSV-backed datasets.

## 3. Create A Shipment

```php
use Daika7ana\Dpd\DTOs\Common\Addressing\AddressLocation;
use Daika7ana\Dpd\DTOs\Shipments\Addresses\LocalShipmentAddress;
use Daika7ana\Dpd\DTOs\Shipments\Parties\ShipmentRecipient;
use Daika7ana\Dpd\DTOs\Shipments\Payloads\ShipmentContent;
use Daika7ana\Dpd\DTOs\Shipments\Payloads\ShipmentPayment;
use Daika7ana\Dpd\DTOs\Shipments\Payloads\ShipmentService;
use Daika7ana\Dpd\DTOs\Shipments\Requests\CreateShipmentRequest;
use Daika7ana\Dpd\Enums\Payments\Payer;

$shipmentRequest = new CreateShipmentRequest(
    recipient: new ShipmentRecipient(
        clientName: 'Receiver SRL',
        privatePerson: false,
        address: new LocalShipmentAddress(
            location: new AddressLocation(
                countryId: 642,
                stateId: null,
                siteId: 68134,
                siteType: null,
                siteName: 'Bucuresti',
                postCode: '010101',
                x: null,
                y: null,
            ),
            streetType: 'Str.',
            streetName: 'Victoriei',
            streetNo: '10',
        ),
    ),
    service: new ShipmentService(serviceId: 2505),
    content: new ShipmentContent(
        contents: 'Electronics',
        package: 'Box',
        parcelsCount: 1,
        totalWeight: 2.5,
    ),
    payment: new ShipmentPayment(Payer::Sender),
);

$shipment = $client->shipments()->create($shipmentRequest);

echo $shipment->id . PHP_EOL;
```

For international destinations, switch to `ForeignShipmentAddress` instead of overloading the local address shape.

## 4. Validate Or Calculate Before Sending Live Traffic

```php
$shipmentValidation = $client->validation()->shipment($shipmentRequest);
$calculationGroups = $client->calculations()->calculate($calculationRequest);
```

Those two calls are the safest pre-flight checks when address quality or pricing rules are still being validated.

## 5. Print Or Track

```php
use Daika7ana\Dpd\DTOs\Common\Addressing\ShipmentParcelRef;
use Daika7ana\Dpd\DTOs\Printing\Payloads\ParcelToPrint;
use Daika7ana\Dpd\DTOs\Printing\Requests\PrintRequest;
use Daika7ana\Dpd\DTOs\Tracking\Models\TrackShipmentParcelRef;
use Daika7ana\Dpd\Enums\Printing\PaperSize;
use Daika7ana\Dpd\Enums\Printing\PrintFormat;

$document = $client->printing()->document(new PrintRequest(
    paperSize: PaperSize::A4,
    parcels: [new ParcelToPrint(new ShipmentParcelRef(id: 'PARCEL123', externalCarrierParcelNumber: null, fullBarcode: null))],
    format: PrintFormat::Pdf,
));

$tracking = $client->tracking()->trackBatched([
    new TrackShipmentParcelRef(id: 'PARCEL-1'),
    new TrackShipmentParcelRef(id: 'PARCEL-2'),
], lastOperationOnly: true);
```

Use `track()` when you are already within DPD's 10-parcel limit. Use `trackBatched()` when you want chunking to stay explicit in the calling code.

## Where To Go Next

- [Examples](EXAMPLES.md) for shorter workflow snippets
- [Usage](USAGE.md) for workflow guidance
- [Resources](RESOURCES.md) for the method-level surface
- [Testing](TESTING.md) for local verification and smoke setup
