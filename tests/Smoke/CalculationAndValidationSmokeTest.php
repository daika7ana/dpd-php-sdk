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
        $phoneResult = $client->validation()->phone($this->sampleRecipientPhone());
        $postCodeResult = $client->validation()->postCode(new ValidatePostCodeRequest(
            postCode: $this->samplePostCode(),
            countryId: $this->sampleCountryId(),
            siteId: $this->sampleSiteId(),
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
