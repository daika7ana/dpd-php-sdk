<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Common\Routing;

final readonly class Tour
{
    public function __construct(
        public ?int $number,
        public ?int $officeId,
        public ?string $ramp,
        public ?string $cell,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            number: isset($data['number']) ? (int) $data['number'] : null,
            officeId: isset($data['officeId']) ? (int) $data['officeId'] : null,
            ramp: isset($data['ramp']) ? (string) $data['ramp'] : null,
            cell: isset($data['cell']) ? (string) $data['cell'] : null,
        );
    }
}
