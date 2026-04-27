# Examples

These examples follow one consistent path: build a client, validate a shipment, create it, then print or track it.

They are intentionally short. For the full reference surface, use [Resources](RESOURCES.md).

## 1. Build The Client

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
```

## 2. Inspect Available Services

```php
$services = $client->services()->list();

foreach ($services as $service) {
    echo $service->id . ': ' . $service->nameEn . PHP_EOL;
}
```

## 3. Build And Validate A Shipment

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

$validation = $client->validation()->shipment($shipmentRequest);
```

Run calculation before creation when pricing needs to be confirmed separately:

```php
$calculationGroups = $client->calculations()->calculate($calculationRequest);
```

## 4. Create The Shipment

```php
$shipment = $client->shipments()->create($shipmentRequest);

echo $shipment->id . PHP_EOL;
```

## 5. Print A Label

```php
use Daika7ana\Dpd\DTOs\Common\Addressing\ShipmentParcelRef;
use Daika7ana\Dpd\DTOs\Printing\Payloads\ParcelToPrint;
use Daika7ana\Dpd\DTOs\Printing\Requests\PrintRequest;
use Daika7ana\Dpd\Enums\Printing\PaperSize;
use Daika7ana\Dpd\Enums\Printing\PrintFormat;

$document = $client->printing()->document(new PrintRequest(
    paperSize: PaperSize::A4,
    parcels: [new ParcelToPrint(new ShipmentParcelRef(id: 'PARCEL123', externalCarrierParcelNumber: null, fullBarcode: null))],
    format: PrintFormat::Pdf,
));
```

Switch to `PrintFormat::Zpl` and `PaperSize::A6` for thermal-printer flows.

## 6. Track Parcels In Batches

```php
use Daika7ana\Dpd\DTOs\Tracking\Models\TrackShipmentParcelRef;

$tracking = $client->tracking()->trackBatched([
    new TrackShipmentParcelRef(id: 'PARCEL-1'),
    new TrackShipmentParcelRef(id: 'PARCEL-2'),
], lastOperationOnly: true);
```

Use `track()` instead when the caller already stays within DPD's 10-parcel limit.