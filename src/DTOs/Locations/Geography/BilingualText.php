<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Locations\Geography;

final readonly class BilingualText
{
    public function __construct(
        public string $local,
        public string $english,
    ) {}

    public static function fromValues(?string $local, ?string $english): self
    {
        return new self(
            local: $local ?? '',
            english: $english ?? '',
        );
    }
}
