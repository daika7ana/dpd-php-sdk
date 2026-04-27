<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Common\Routing;

final readonly class RoutingInformation
{
    public function __construct(
        public ?Tour $tour,
        public ?int $officeId,
        public ?int $hubId,
        public ?int $priority,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            tour: is_array($data['tour'] ?? null) ? Tour::fromArray($data['tour']) : null,
            officeId: isset($data['officeId']) ? (int) $data['officeId'] : null,
            hubId: isset($data['hubId']) ? (int) $data['hubId'] : null,
            priority: isset($data['priority']) ? (int) $data['priority'] : null,
        );
    }
}
