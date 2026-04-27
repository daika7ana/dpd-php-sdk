<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\Tests\Smoke;

use Daika7ana\Dpd\Client;
use Daika7ana\Dpd\Config\ClientConfig;
use Daika7ana\Dpd\DTOs\Calculations\Payloads\CalculationContent;
use Daika7ana\Dpd\DTOs\Calculations\Payloads\CalculationRecipient;
use Daika7ana\Dpd\DTOs\Calculations\Payloads\CalculationSender;
use Daika7ana\Dpd\DTOs\Calculations\Payloads\CalculationService;
use Daika7ana\Dpd\DTOs\Calculations\Requests\CalculationRequest;
use Daika7ana\Dpd\DTOs\Common\Addressing\AddressLocation;
use Daika7ana\Dpd\DTOs\Common\Addressing\ShipmentParcelRef;
use Daika7ana\Dpd\DTOs\Common\Addressing\ShipmentPhoneNumber;
use Daika7ana\Dpd\DTOs\Locations\Requests\NearestOfficesRequest;
use Daika7ana\Dpd\DTOs\Locations\Requests\SiteSearchRequest;
use Daika7ana\Dpd\DTOs\Locations\Requests\StreetSearchRequest;
use Daika7ana\Dpd\DTOs\Locations\Geography\Site;
use Daika7ana\Dpd\DTOs\Locations\Geography\Street;
use Daika7ana\Dpd\DTOs\Payments\Requests\PayoutRequest;
use Daika7ana\Dpd\DTOs\Pickup\Requests\PickupRequest;
use Daika7ana\Dpd\DTOs\Pickup\Requests\PickupTermsRequest;
use Daika7ana\Dpd\DTOs\Printing\Payloads\ParcelToPrint;
use Daika7ana\Dpd\DTOs\Printing\Requests\PrintRequest;
use Daika7ana\Dpd\DTOs\Shipments\Addresses\LocalShipmentAddress;
use Daika7ana\Dpd\DTOs\Shipments\Payloads\ShipmentContent;
use Daika7ana\Dpd\DTOs\Shipments\Payloads\ShipmentPayment;
use Daika7ana\Dpd\DTOs\Shipments\Payloads\ShipmentService;
use Daika7ana\Dpd\DTOs\Shipments\Parties\ShipmentRecipient;
use Daika7ana\Dpd\DTOs\Shipments\Requests\CancelShipmentRequest;
use Daika7ana\Dpd\DTOs\Shipments\Requests\CreateShipmentRequest;
use Daika7ana\Dpd\DTOs\Tracking\Models\TrackShipmentParcelRef;
use Daika7ana\Dpd\DTOs\Tracking\Requests\TrackRequest;
use Daika7ana\Dpd\Enums\Payments\Payer;
use Daika7ana\Dpd\Enums\Pickup\PickupScope;
use Daika7ana\Dpd\Enums\Printing\PaperSize;
use Daika7ana\Dpd\Enums\Printing\PrintFormat;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Throwable;

abstract class SmokeTestCase extends TestCase
{
    private const DEFAULT_COUNTRY_ID = 642;

    private const DEFAULT_SITE_SEARCH = 'Bucuresti';

    private const DEFAULT_STREET_SEARCH = 'Victoriei';

    private const DEFAULT_STREET_NO = '1';

    private const DEFAULT_POST_CODE = '010101';

    private const DEFAULT_RECIPIENT_NAME = 'Smoke Test Receiver SRL';

    private const DEFAULT_RECIPIENT_CONTACT_NAME = 'Smoke Test Contact';

    private const DEFAULT_RECIPIENT_PHONE = '0722123456';

    private const DEFAULT_RECIPIENT_EMAIL = 'smoke@example.test';

    private ?Client $client = null;

    private ?Site $sampleSite = null;

    private ?Street $sampleStreet = null;

    private ?AddressLocation $sampleAddressLocation = null;

    private ?int $sampleServiceId = null;

    private ?ShipmentParcelRef $selfSeededParcelRef = null;

    private ?string $selfSeededShipmentId = null;

    protected function client(): Client
    {
        if ($this->client instanceof Client) {
            return $this->client;
        }

        $config = ClientConfig::fromEnvironment();

        if ($config->username === '' || $config->password === '') {
            $this->markTestSkipped('DPD smoke credentials are not configured.');
        }

        $this->client = Client::create(config: $config);

        return $this->client;
    }

    protected function requireWriteAccess(string $workflow): void
    {
        if (!$this->envBool('DPD_SMOKE_ENABLE_WRITES', false)) {
            $this->markTestSkipped(sprintf(
                'Skipping %s because DPD_SMOKE_ENABLE_WRITES is not enabled.',
                $workflow,
            ));
        }
    }

    protected function tearDown(): void
    {
        $shipmentId = $this->selfSeededShipmentId;

        $this->selfSeededShipmentId = null;
        $this->selfSeededParcelRef = null;

        if ($shipmentId !== null) {
            try {
                $this->client()->shipments()->cancel(new CancelShipmentRequest($shipmentId, 'Smoke test self-seeded cleanup'));
            } catch (Throwable) {
            }
        }

        parent::tearDown();
    }

    protected function sampleNearestOfficesRequest(): NearestOfficesRequest
    {
        return new NearestOfficesRequest(
            address: $this->sampleLocalAddress(),
            distance: $this->envInt('DPD_SMOKE_OFFICE_DISTANCE', 10000),
            limit: $this->envInt('DPD_SMOKE_OFFICE_LIMIT', 5),
        );
    }

    protected function sampleCalculationRequest(): CalculationRequest
    {
        return new CalculationRequest(
            recipient: new CalculationRecipient(
                privatePerson: false,
                addressLocation: $this->sampleAddressLocation(),
            ),
            service: new CalculationService(
                serviceIds: [$this->sampleServiceId()],
                pickupDate: new DateTimeImmutable('today'),
                autoAdjustPickupDate: true,
            ),
            content: new CalculationContent(
                parcelsCount: 1,
                totalWeight: $this->envFloat('DPD_SMOKE_TOTAL_WEIGHT', 1.0),
            ),
            payment: new ShipmentPayment(Payer::Sender),
            sender: new CalculationSender(
                privatePerson: false,
                addressLocation: $this->sampleAddressLocation(),
            ),
        );
    }

    protected function samplePrintRequest(?ShipmentParcelRef $parcel = null): PrintRequest
    {
        $format = $this->samplePrintFormat();

        return new PrintRequest(
            paperSize: $format === PrintFormat::Zpl ? PaperSize::A6 : PaperSize::A4,
            parcels: [new ParcelToPrint($parcel ?? $this->sampleParcelRef('DPD_SMOKE_PRINT_PARCEL_ID', 'DPD_SMOKE_TRACK_PARCEL_ID'))],
            format: $format,
        );
    }

    protected function sampleTrackRequest(): TrackRequest
    {
        return new TrackRequest([
            TrackShipmentParcelRef::fromParcelRef($this->sampleParcelRef('DPD_SMOKE_TRACK_PARCEL_ID', 'DPD_SMOKE_PRINT_PARCEL_ID')),
        ], true);
    }

    protected function sampleShipmentRequest(): CreateShipmentRequest
    {
        return new CreateShipmentRequest(
            recipient: new ShipmentRecipient(
                phone1: new ShipmentPhoneNumber($this->sampleRecipientPhone()),
                clientName: $this->sampleRecipientName(),
                contactName: $this->sampleRecipientContactName(),
                email: $this->env('DPD_SMOKE_RECIPIENT_EMAIL', self::DEFAULT_RECIPIENT_EMAIL),
                privatePerson: false,
                address: $this->sampleLocalAddress(),
            ),
            service: new ShipmentService(
                serviceId: $this->sampleServiceId(),
                pickupDate: new DateTimeImmutable('today'),
                autoAdjustPickupDate: true,
            ),
            content: new ShipmentContent(
                contents: $this->env('DPD_SMOKE_CONTENTS', 'Smoke test parcel') ?? 'Smoke test parcel',
                package: $this->env('DPD_SMOKE_PACKAGE', 'Box') ?? 'Box',
                parcelsCount: 1,
                totalWeight: $this->envFloat('DPD_SMOKE_TOTAL_WEIGHT', 1.0),
            ),
            payment: new ShipmentPayment(Payer::Sender),
            ref1: $this->smokeReference('SHIP'),
        );
    }

    protected function samplePickupTermsRequest(): PickupTermsRequest
    {
        return new PickupTermsRequest(
            serviceId: $this->sampleServiceId(),
            startingDate: new DateTimeImmutable('today'),
            sender: new CalculationSender(
                privatePerson: false,
                addressLocation: $this->sampleAddressLocation(),
            ),
            senderHasPayment: true,
        );
    }

    protected function samplePickupRequest(string $shipmentId): PickupRequest
    {
        return new PickupRequest(
            visitEndTime: $this->env('DPD_SMOKE_PICKUP_VISIT_END_TIME', '17:00') ?? '17:00',
            pickupDateTime: new DateTimeImmutable('now'),
            autoAdjustPickupDate: true,
            pickupScope: PickupScope::ExplicitShipmentIdList,
            explicitShipmentIdList: [$shipmentId],
            contactName: $this->env('DPD_SMOKE_PICKUP_CONTACT_NAME', $this->sampleRecipientName()),
            phoneNumber: new ShipmentPhoneNumber($this->sampleRecipientPhone()),
        );
    }

    protected function samplePayoutRequest(): PayoutRequest
    {
        $days = $this->envInt('DPD_SMOKE_PAYOUT_DAYS', 30) ?? 30;

        return new PayoutRequest(
            fromDate: new DateTimeImmutable(sprintf('-%d days', $days)),
            toDate: new DateTimeImmutable('now'),
            includeDetails: $this->envBool('DPD_SMOKE_PAYOUT_INCLUDE_DETAILS', true),
        );
    }

    protected function sampleAddressLocation(): AddressLocation
    {
        if ($this->sampleAddressLocation instanceof AddressLocation) {
            return $this->sampleAddressLocation;
        }

        $site = $this->sampleSite();

        $this->sampleAddressLocation = new AddressLocation(
            countryId: $site->countryId,
            stateId: null,
            siteId: $site->id,
            siteType: null,
            siteName: null,
            postCode: $this->env('DPD_SMOKE_POST_CODE', $site->postCode ?? self::DEFAULT_POST_CODE),
            x: $site->x,
            y: $site->y,
        );

        return $this->sampleAddressLocation;
    }

    protected function sampleLocalAddress(): LocalShipmentAddress
    {
        $street = $this->sampleStreet();

        return new LocalShipmentAddress(
            location: $this->sampleAddressLocation(),
            streetId: $street->id,
            streetType: $this->env('DPD_SMOKE_STREET_TYPE', $street->type->local !== '' ? $street->type->local : null),
            streetName: $this->env('DPD_SMOKE_STREET_NAME', $street->name->local !== '' ? $street->name->local : null),
            streetNo: $this->env('DPD_SMOKE_STREET_NO', self::DEFAULT_STREET_NO),
            blockNo: $this->env('DPD_SMOKE_BLOCK_NO'),
        );
    }

    protected function sampleServiceId(): int
    {
        if ($this->sampleServiceId !== null) {
            return $this->sampleServiceId;
        }

        $configuredServiceId = $this->envInt('DPD_SMOKE_SERVICE_ID');

        if ($configuredServiceId !== null) {
            $this->sampleServiceId = $configuredServiceId;

            return $this->sampleServiceId;
        }

        $service = $this->client()->services()->list()->first();

        if ($service === null) {
            $this->markTestSkipped('Skipping smoke test because no DPD services are available for the configured account.');
        }

        $this->sampleServiceId = $service->id;

        return $this->sampleServiceId;
    }

    protected function sampleCountryId(): int
    {
        return $this->sampleAddressLocation()->countryId ?? self::DEFAULT_COUNTRY_ID;
    }

    protected function sampleSiteId(): int
    {
        $siteId = $this->sampleAddressLocation()->siteId;

        if ($siteId === null) {
            $this->markTestSkipped('Skipping smoke test because the sample site could not be resolved.');
        }

        return $siteId;
    }

    protected function samplePostCode(): string
    {
        $postCode = $this->sampleAddressLocation()->postCode;

        if ($postCode === null || $postCode === '') {
            $this->markTestSkipped('Skipping smoke test because the sample postcode could not be resolved.');
        }

        return $postCode;
    }

    protected function sampleParcelRef(string ...$keys): ShipmentParcelRef
    {
        foreach ($keys as $key) {
            $value = $this->env($key);

            if ($value !== null) {
                return new ShipmentParcelRef($value, null, null);
            }
        }

        if ($this->envBool('DPD_SMOKE_ENABLE_WRITES', false)) {
            return $this->selfSeededParcelRef();
        }

        $this->markTestSkipped(sprintf(
            'Skipping smoke test because none of the parcel reference env keys are configured: %s.',
            implode(', ', $keys),
        ));
    }

    protected function smokeReference(string $prefix): string
    {
        $resolvedPrefix = $this->env('DPD_SMOKE_REF_PREFIX', $prefix) ?? $prefix;

        return sprintf('%s-%s', $resolvedPrefix, gmdate('YmdHis'));
    }

    protected function env(string $key, ?string $default = null): ?string
    {
        $value = getenv($key);

        if ($value === false || $value === '') {
            return $default;
        }

        return $value;
    }

    protected function envInt(string $key, ?int $default = null): ?int
    {
        $value = $this->env($key);

        if ($value === null) {
            return $default;
        }

        return (int) $value;
    }

    protected function envFloat(string $key, float $default): float
    {
        $value = $this->env($key);

        if ($value === null) {
            return $default;
        }

        return (float) $value;
    }

    protected function envBool(string $key, bool $default): bool
    {
        $value = $this->env($key);

        if ($value === null) {
            return $default;
        }

        return in_array(strtolower($value), ['1', 'true', 'yes', 'on'], true);
    }

    protected function requireStringEnv(string $key): string
    {
        $value = $this->env($key);

        if ($value === null) {
            $this->markTestSkipped(sprintf('Skipping smoke test because %s is not configured.', $key));
        }

        return $value;
    }

    protected function requireIntEnv(string $key): int
    {
        return (int) $this->requireStringEnv($key);
    }

    protected function sampleRecipientName(): string
    {
        return $this->env('DPD_SMOKE_RECIPIENT_NAME', self::DEFAULT_RECIPIENT_NAME) ?? self::DEFAULT_RECIPIENT_NAME;
    }

    protected function sampleRecipientPhone(): string
    {
        return $this->env('DPD_SMOKE_RECIPIENT_PHONE', self::DEFAULT_RECIPIENT_PHONE) ?? self::DEFAULT_RECIPIENT_PHONE;
    }

    protected function sampleRecipientContactName(): string
    {
        return $this->env('DPD_SMOKE_RECIPIENT_CONTACT_NAME', self::DEFAULT_RECIPIENT_CONTACT_NAME) ?? self::DEFAULT_RECIPIENT_CONTACT_NAME;
    }

    private function sampleSite(): Site
    {
        if ($this->sampleSite instanceof Site) {
            return $this->sampleSite;
        }

        $configuredSiteId = $this->envInt('DPD_SMOKE_SITE_ID');

        if ($configuredSiteId !== null) {
            $configuredSite = $this->client()->locations()->getSite($configuredSiteId);

            if ($configuredSite instanceof Site) {
                $this->sampleSite = $configuredSite;

                return $this->sampleSite;
            }
        }

        $countryId = $this->envInt('DPD_SMOKE_COUNTRY_ID', self::DEFAULT_COUNTRY_ID) ?? self::DEFAULT_COUNTRY_ID;
        $siteName = $this->env('DPD_SMOKE_SITE_NAME', self::DEFAULT_SITE_SEARCH) ?? self::DEFAULT_SITE_SEARCH;
        $postCode = $this->env('DPD_SMOKE_POST_CODE');

        $sites = $this->client()->locations()->findSites(new SiteSearchRequest(
            countryId: $countryId,
            name: $siteName,
            postCode: $postCode,
        ));

        $site = $sites->first();

        if (!$site instanceof Site) {
            $this->markTestSkipped(sprintf(
                'Skipping smoke test because a sample site could not be resolved for country %d and search term "%s".',
                $countryId,
                $siteName,
            ));
        }

        $this->sampleSite = $site;

        return $this->sampleSite;
    }

    private function sampleStreet(): Street
    {
        if ($this->sampleStreet instanceof Street) {
            return $this->sampleStreet;
        }

        $site = $this->sampleSite();
        $streetName = $this->env('DPD_SMOKE_STREET_NAME', self::DEFAULT_STREET_SEARCH);
        $streetType = $this->env('DPD_SMOKE_STREET_TYPE');

        $streets = $this->client()->locations()->findStreets(new StreetSearchRequest(
            siteId: $site->id,
            name: $streetName,
            type: $streetType,
        ));

        $street = $streets->first();

        if (!$street instanceof Street) {
            $street = $this->client()->locations()->findStreets(new StreetSearchRequest(siteId: $site->id))->first();
        }

        if (!$street instanceof Street) {
            $this->markTestSkipped(sprintf(
                'Skipping smoke test because a sample street could not be resolved for site %d.',
                $site->id,
            ));
        }

        $this->sampleStreet = $street;

        return $this->sampleStreet;
    }

    private function samplePrintFormat(): PrintFormat
    {
        return strtolower($this->env('DPD_SMOKE_PRINT_FORMAT', 'pdf') ?? 'pdf') === 'zpl'
            ? PrintFormat::Zpl
            : PrintFormat::Pdf;
    }

    private function selfSeededParcelRef(): ShipmentParcelRef
    {
        if ($this->selfSeededParcelRef instanceof ShipmentParcelRef) {
            return $this->selfSeededParcelRef;
        }

        $this->requireWriteAccess('tracking and printing smoke tests without configured parcel references');

        $shipment = $this->client()->shipments()->create($this->sampleShipmentRequest());
        $parcelId = $shipment->parcels->first()?->id;

        if ($parcelId === null || $parcelId === '') {
            $this->markTestSkipped('Skipping smoke test because the self-seeded shipment did not return a parcel id.');
        }

        $this->selfSeededShipmentId = $shipment->id;
        $this->selfSeededParcelRef = new ShipmentParcelRef($parcelId, null, null);

        return $this->selfSeededParcelRef;
    }
}
