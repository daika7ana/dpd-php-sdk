<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Locations\Responses;

use Daika7ana\Dpd\DTOs\Common\Support\Collection;
use Daika7ana\Dpd\DTOs\Locations\Offices\OfficeResult;

final readonly class NearestOfficesResponse
{
    /**
     * @param Collection<int, OfficeResult> $offices
     */
    public function __construct(
        public Collection $offices,
        public ?float $x,
        public ?float $y,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $offices = [];

        foreach ($data['offices'] ?? [] as $rawOffice) {
            if (is_array($rawOffice)) {
                $offices[] = OfficeResult::fromArray($rawOffice);
            }
        }

        return new self(
            offices: new Collection($offices),
            x: isset($data['x']) ? (float) $data['x'] : null,
            y: isset($data['y']) ? (float) $data['y'] : null,
        );
    }
}
