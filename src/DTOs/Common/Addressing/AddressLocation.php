<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Common\Addressing;

use Daika7ana\Dpd\Support\PayloadFilter;

final readonly class AddressLocation
{
    public function __construct(
        public ?int $countryId = null,
        public ?string $stateId = null,
        public ?int $siteId = null,
        public ?string $siteType = null,
        public ?string $siteName = null,
        public ?string $postCode = null,
        public ?float $x = null,
        public ?float $y = null,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            countryId: isset($data['countryId']) ? (int) $data['countryId'] : null,
            stateId: isset($data['stateId']) ? (string) $data['stateId'] : null,
            siteId: isset($data['siteId']) ? (int) $data['siteId'] : null,
            siteType: isset($data['siteType']) ? (string) $data['siteType'] : null,
            siteName: isset($data['siteName']) ? (string) $data['siteName'] : null,
            postCode: isset($data['postCode']) ? (string) $data['postCode'] : null,
            x: isset($data['x']) ? (float) $data['x'] : null,
            y: isset($data['y']) ? (float) $data['y'] : null,
        );
    }

    public function hasCoordinates(): bool
    {
        return $this->x !== null && $this->y !== null;
    }

    /**
     * @return array<string, float|int|string>
     */
    public function toArray(): array
    {
        return PayloadFilter::removeNulls([
            'countryId' => $this->countryId,
            'stateId' => $this->stateId,
            'siteId' => $this->siteId,
            'siteType' => $this->siteType,
            'siteName' => $this->siteName,
            'postCode' => $this->postCode,
            'x' => $this->x,
            'y' => $this->y,
        ]);
    }
}
