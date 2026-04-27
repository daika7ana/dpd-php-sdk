<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\Tests\Smoke;

final class ServicesSmokeTest extends SmokeTestCase
{
    public function testCanFetchServicesFromLiveApi(): void
    {
        $services = $this->client()->services()->list();

        $this->assertGreaterThan(0, $services->count());
    }
}
