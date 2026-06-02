<?php

declare(strict_types=1);

namespace Daika7ana\Dpd\Tests\Unit\DTOs;

use Daika7ana\Dpd\DTOs\Common\Errors\DpdError;
use PHPUnit\Framework\TestCase;

final class DpdErrorTest extends TestCase
{
    public function testFromArrayMapsAllFields(): void
    {
        $data = [
            'code' => 100,
            'message' => 'Validation failed',
            'id' => 'err-val-100',
            'context' => 'parcel weight',
            'component' => 'shipment',
        ];

        $error = DpdError::fromArray($data);

        $this->assertSame(100, $error->code);
        $this->assertSame('Validation failed', $error->message);
        $this->assertSame('err-val-100', $error->id);
        $this->assertSame('parcel weight', $error->context);
        $this->assertSame('shipment', $error->component);
        $this->assertSame($data, $error->raw);
    }

    public function testFromArrayUsesDefaultsForMissingFields(): void
    {
        $error = DpdError::fromArray([]);

        $this->assertSame(0, $error->code);
        $this->assertSame('DPD API error.', $error->message);
        $this->assertSame('', $error->id);
        $this->assertNull($error->context);
        $this->assertNull($error->component);
    }

    public function testIsValidationRelatedReturnsTrueForValidationCodes(): void
    {
        $validationCodes = [100, 120, 160, 180, 300, 400, 410, 415, 420, 425, 430, 435, 440, 445, 450, 455, 460, 500, 510, 515, 520, 600, 605, 610, 615, 620, 630, 700, 710, 800, 805, 810];

        foreach ($validationCodes as $code) {
            $error = DpdError::fromArray(['code' => $code, 'message' => 'test', 'id' => 'test']);
            $this->assertTrue($error->isValidationRelated(), "Code {$code} should be validation-related");
        }
    }

    public function testIsValidationRelatedReturnsFalseForNonValidationCodes(): void
    {
        $error = DpdError::fromArray(['code' => 999, 'message' => 'test', 'id' => 'test']);

        $this->assertFalse($error->isValidationRelated());
    }

    public function testIsAuthenticationRelatedReturnsTrueForAuthKeywords(): void
    {
        $authMessages = [
            'Authentication failed',
            'Invalid password provided',
            'Credential error',
            'Logged user not found',
            'Access rights denied',
        ];

        foreach ($authMessages as $message) {
            $error = DpdError::fromArray(['code' => 1, 'message' => $message, 'id' => 'test']);
            $this->assertTrue($error->isAuthenticationRelated(), "Message '{$message}' should be auth-related");
        }
    }

    public function testIsAuthenticationRelatedReturnsFalseForNonAuthMessages(): void
    {
        $error = DpdError::fromArray(['code' => 1, 'message' => 'Parcel not found', 'id' => 'test']);

        $this->assertFalse($error->isAuthenticationRelated());
    }
}
