<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\Tests\Smoke;

final class LocationsSmokeTest extends SmokeTestCase
{
    public function testCanFindNearestOfficesFromLiveApi(): void
    {
        $response = $this->client()->locations()->findNearestOffices($this->sampleNearestOfficesRequest());

        $this->assertGreaterThan(0, $response->offices->count());
        $this->assertGreaterThan(0, $response->offices->first()?->office->id ?? 0);
    }
}
