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
use Daika7ana\Dpd\DTOs\Shipments\Requests\CreateShipmentRequest;
use Daika7ana\Dpd\DTOs\Tracking\Models\TrackShipmentParcelRef;
use Daika7ana\Dpd\DTOs\Tracking\Requests\TrackRequest;
use Daika7ana\Dpd\Enums\Payments\Payer;
use Daika7ana\Dpd\Enums\Pickup\PickupScope;
use Daika7ana\Dpd\Enums\Printing\PaperSize;
use Daika7ana\Dpd\Enums\Printing\PrintFormat;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

abstract class SmokeTestCase extends TestCase
{
    private ?Client $client = null;

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
                phone1: new ShipmentPhoneNumber($this->requireStringEnv('DPD_SMOKE_RECIPIENT_PHONE')),
                clientName: $this->requireStringEnv('DPD_SMOKE_RECIPIENT_NAME'),
                email: $this->env('DPD_SMOKE_RECIPIENT_EMAIL'),
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
            contactName: $this->env('DPD_SMOKE_PICKUP_CONTACT_NAME', $this->requireStringEnv('DPD_SMOKE_RECIPIENT_NAME')),
            phoneNumber: new ShipmentPhoneNumber($this->requireStringEnv('DPD_SMOKE_RECIPIENT_PHONE')),
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
        return AddressLocation::fromArray([
            'countryId' => $this->requireIntEnv('DPD_SMOKE_COUNTRY_ID'),
            'siteId' => $this->requireIntEnv('DPD_SMOKE_SITE_ID'),
            'siteName' => $this->requireStringEnv('DPD_SMOKE_SITE_NAME'),
            'postCode' => $this->requireStringEnv('DPD_SMOKE_POST_CODE'),
        ]);
    }

    protected function sampleLocalAddress(): LocalShipmentAddress
    {
        return new LocalShipmentAddress(
            location: $this->sampleAddressLocation(),
            streetType: $this->requireStringEnv('DPD_SMOKE_STREET_TYPE'),
            streetName: $this->requireStringEnv('DPD_SMOKE_STREET_NAME'),
            streetNo: $this->requireStringEnv('DPD_SMOKE_STREET_NO'),
            blockNo: $this->env('DPD_SMOKE_BLOCK_NO'),
        );
    }

    protected function sampleServiceId(): int
    {
        return $this->requireIntEnv('DPD_SMOKE_SERVICE_ID');
    }

    protected function sampleParcelRef(string ...$keys): ShipmentParcelRef
    {
        foreach ($keys as $key) {
            $value = $this->env($key);

            if ($value !== null) {
                return new ShipmentParcelRef($value, null, null);
            }
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

    private function samplePrintFormat(): PrintFormat
    {
        return strtolower($this->env('DPD_SMOKE_PRINT_FORMAT', 'pdf') ?? 'pdf') === 'zpl'
            ? PrintFormat::Zpl
            : PrintFormat::Pdf;
    }
}
