<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Common\Documents;

final readonly class BinaryContent
{
    public function __construct(
        public string $contents,
        public string $mimeType,
    ) {}

    public function asBase64(): string
    {
        return base64_encode($this->contents);
    }

    public function asString(): string
    {
        return $this->contents;
    }
}
