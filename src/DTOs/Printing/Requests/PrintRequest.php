<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\DTOs\Printing\Requests;

use Daika7ana\Dpd\DTOs\Printing\Payloads\ParcelToPrint;
use Daika7ana\Dpd\Enums\Printing\AdditionalWaybillSenderCopy;
use Daika7ana\Dpd\Enums\Printing\Dpi;
use Daika7ana\Dpd\Enums\Printing\PaperSize;
use Daika7ana\Dpd\Enums\Printing\PrintFormat;
use Daika7ana\Dpd\Support\PayloadFilter;

final readonly class PrintRequest
{
    /**
     * @param list<ParcelToPrint> $parcels
     */
    public function __construct(
        public PaperSize $paperSize,
        public array $parcels,
        public ?PrintFormat $format = null,
        public ?string $printerName = null,
        public ?Dpi $dpi = null,
        public ?AdditionalWaybillSenderCopy $additionalWaybillSenderCopy = null,
    ) {}

    /**
     * @return array<string, list<array<string, mixed>>|string>
     */
    public function toArray(): array
    {
        return PayloadFilter::removeNulls([
            'format' => $this->format?->value,
            'paperSize' => $this->paperSize->value,
            'parcels' => array_map(
                static fn(ParcelToPrint $parcel): array => $parcel->toArray(),
                $this->parcels,
            ),
            'printerName' => $this->printerName,
            'dpi' => $this->dpi?->value,
            'additionalWaybillSenderCopy' => $this->additionalWaybillSenderCopy?->value,
        ]);
    }
}
