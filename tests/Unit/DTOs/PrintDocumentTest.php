<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\Tests\Unit\DTOs;

use Daika7ana\Dpd\DTOs\Common\Documents\BinaryContent;
use Daika7ana\Dpd\DTOs\Printing\Documents\PrintDocument;
use Daika7ana\Dpd\Enums\Printing\PrintFormat;
use PHPUnit\Framework\TestCase;

final class PrintDocumentTest extends TestCase
{
    public function testFromBinaryMapsPdfMimeType(): void
    {
        $binary = new BinaryContent('%PDF-content', 'application/pdf');

        $document = PrintDocument::fromBinary($binary);

        $this->assertSame('%PDF-content', $document->contents);
        $this->assertSame('application/pdf', $document->mimeType);
        $this->assertSame(PrintFormat::Pdf, $document->format);
        $this->assertTrue($document->isPdf());
        $this->assertFalse($document->isZpl());
    }

    public function testFromBinaryMapsZplMimeType(): void
    {
        $binary = new BinaryContent('^XA^FO...^XZ', 'text/plain');

        $document = PrintDocument::fromBinary($binary);

        $this->assertSame('^XA^FO...^XZ', $document->contents);
        $this->assertSame('text/plain', $document->mimeType);
        $this->assertSame(PrintFormat::Zpl, $document->format);
        $this->assertFalse($document->isPdf());
        $this->assertTrue($document->isZpl());
    }

    public function testFromBinaryReturnsNullFormatForUnknownMimeType(): void
    {
        $binary = new BinaryContent('content', 'image/png');

        $document = PrintDocument::fromBinary($binary);

        $this->assertSame('image/png', $document->mimeType);
        $this->assertNull($document->format);
        $this->assertFalse($document->isPdf());
        $this->assertFalse($document->isZpl());
    }

    public function testConstructorSetsAllFields(): void
    {
        $document = new PrintDocument(
            contents: 'test-content',
            mimeType: 'application/pdf',
            format: PrintFormat::Pdf,
        );

        $this->assertSame('test-content', $document->contents);
        $this->assertSame('application/pdf', $document->mimeType);
        $this->assertSame(PrintFormat::Pdf, $document->format);
    }

    public function testConstructorAllowsNullFormat(): void
    {
        $document = new PrintDocument(
            contents: 'test',
            mimeType: 'application/octet-stream',
        );

        $this->assertNull($document->format);
    }
}
