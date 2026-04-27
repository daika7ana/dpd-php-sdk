<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Printing\Requests;

use Daika7ana\Dpd\Enums\Printing\Dpi;
use Daika7ana\Dpd\Enums\Printing\PrintFormat;
use Daika7ana\Dpd\Support\PayloadFilter;

final readonly class PrintVoucherRequest
{
    /**
     * @param list<string> $shipmentIds
     */
    public function __construct(
        public array $shipmentIds,
        public ?string $printerName = null,
        public ?PrintFormat $format = null,
        public ?Dpi $dpi = null,
    ) {}

    /**
     * @return array<string, list<string>|string>
     */
    public function toArray(): array
    {
        return PayloadFilter::removeNulls([
            'shipmentIds' => $this->shipmentIds,
            'printerName' => $this->printerName,
            'format' => $this->format?->value,
            'dpi' => $this->dpi?->value,
        ]);
    }
}
