<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Common\Addressing;

final readonly class ShipmentParcelSize
{
    public function __construct(
        public int $width,
        public int $depth,
        public int $height,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            width: (int) ($data['width'] ?? 0),
            depth: (int) ($data['depth'] ?? 0),
            height: (int) ($data['height'] ?? 0),
        );
    }

    /**
     * @return array{width: int, depth: int, height: int}
     */
    public function toArray(): array
    {
        return [
            'width' => $this->width,
            'depth' => $this->depth,
            'height' => $this->height,
        ];
    }
}
