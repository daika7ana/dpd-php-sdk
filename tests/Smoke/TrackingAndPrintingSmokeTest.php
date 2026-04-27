<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\Tests\Smoke;

final class TrackingAndPrintingSmokeTest extends SmokeTestCase
{
    public function testCanTrackConfiguredParcelFromLiveApi(): void
    {
        $response = $this->client()->tracking()->track($this->sampleTrackRequest());

        $this->assertGreaterThan(0, $response->parcels->count());
    }

    public function testCanPrintConfiguredParcelLabelFromLiveApi(): void
    {
        $document = $this->client()->printing()->document($this->samplePrintRequest());

        $this->assertNotSame('', $document->contents);
        $this->assertTrue($document->isPdf() || $document->isZpl());
    }
}
