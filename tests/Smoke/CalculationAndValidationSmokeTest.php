<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\Tests\Smoke;

use Daika7ana\Dpd\DTOs\Validation\Requests\ValidatePostCodeRequest;

final class CalculationAndValidationSmokeTest extends SmokeTestCase
{
    public function testCanValidateConfiguredAddressPhonePostCodeAndShipment(): void
    {
        $client = $this->client();

        $addressResult = $client->validation()->address($this->sampleLocalAddress());
        $phoneResult = $client->validation()->phone($this->requireStringEnv('DPD_SMOKE_RECIPIENT_PHONE'));
        $postCodeResult = $client->validation()->postCode(new ValidatePostCodeRequest(
            postCode: $this->requireStringEnv('DPD_SMOKE_POST_CODE'),
            countryId: $this->requireIntEnv('DPD_SMOKE_COUNTRY_ID'),
            siteId: $this->requireIntEnv('DPD_SMOKE_SITE_ID'),
        ));
        $shipmentResult = $client->validation()->shipment($this->sampleShipmentRequest());

        $this->assertTrue($addressResult->valid);
        $this->assertTrue($phoneResult->valid);
        $this->assertTrue($postCodeResult->valid);
        $this->assertTrue($shipmentResult->valid);
    }

    public function testCanCalculateAgainstLiveApi(): void
    {
        $calculations = $this->client()->calculations()->calculate($this->sampleCalculationRequest());

        $this->assertGreaterThan(0, $calculations->count());
        $this->assertGreaterThan(0, $calculations->first()?->count() ?? 0);
    }
}
