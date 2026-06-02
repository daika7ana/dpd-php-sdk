<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\Tests\Unit\DTOs;

use Daika7ana\Dpd\DTOs\Printing\Responses\ExtendedPrintResponse;
use Daika7ana\Dpd\Enums\Printing\PrintFormat;
use PHPUnit\Framework\TestCase;

final class ExtendedPrintResponseTest extends TestCase
{
    public function testFromArrayMapsDataAndLabels(): void
    {
        $data = [
            'data' => base64_encode('%PDF-test-content'),
            'printLabelsInfo' => [
                ['parcelId' => 'P001', 'hubId' => 5, 'officeId' => 12],
                ['parcelId' => 'P002', 'hubId' => 6, 'officeId' => 13],
            ],
        ];

        $response = ExtendedPrintResponse::fromArray($data);

        $this->assertSame(base64_encode('%PDF-test-content'), $response->data);
        $this->assertCount(2, $response->printLabelsInfo);
        $this->assertSame('P001', $response->printLabelsInfo->first()?->parcelId);
    }

    public function testFromArrayHandlesMissingLabels(): void
    {
        $data = ['data' => base64_encode('test')];

        $response = ExtendedPrintResponse::fromArray($data);

        $this->assertCount(0, $response->printLabelsInfo);
    }

    public function testAsDocumentDecodesBase64Content(): void
    {
        $pdfContent = '%PDF-1.4 test content';
        $data = ['data' => base64_encode($pdfContent)];

        $response = ExtendedPrintResponse::fromArray($data);
        $document = $response->asDocument();

        $this->assertSame($pdfContent, $document->contents);
        $this->assertSame('application/pdf', $document->mimeType);
        $this->assertSame(PrintFormat::Pdf, $document->format);
    }

    public function testAsDocumentReturnsEmptyContentsForInvalidBase64(): void
    {
        $data = ['data' => '!!!invalid-base64!!!'];

        $response = ExtendedPrintResponse::fromArray($data);
        $document = $response->asDocument();

        $this->assertSame('', $document->contents);
        $this->assertSame('application/pdf', $document->mimeType);
    }

    public function testFromArrayFiltersNonArrayLabels(): void
    {
        $data = [
            'data' => base64_encode('test'),
            'printLabelsInfo' => [
                ['parcelId' => 'P001'],
                'invalid',
                null,
            ],
        ];

        $response = ExtendedPrintResponse::fromArray($data);

        $this->assertCount(1, $response->printLabelsInfo);
    }
}
