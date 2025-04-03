<?php

use PHPUnit\Framework\TestCase;
use App\Service\ValueHandler;

class ValueHandlerTest extends TestCase
{
    private $valueHandler;

    protected function setUp(): void
    {
        $this->valueHandler = new ValueHandler();
    }

    public function testSanitizeValueRemovesCurrencySymbol()
    {
        $value = '£123.45';
        $sanitizedValue = $this->getFromProtectedMethod('sanitizeValue', [$value]);
        $this->assertEquals('123.45', $sanitizedValue);
    }

    public function testSanitizeValueTrimsSpaces()
    {
        $value = '  Test Value  ';
        $sanitizedValue = $this->getFromProtectedMethod('sanitizeValue', [$value]);
        $this->assertEquals('Test Value', $sanitizedValue);
    }

    public function testSanitizeValueHandlesNull()
    {
        $value = null;
        $sanitizedValue = $this->getFromProtectedMethod('sanitizeValue', [$value]);
        $this->assertNull($sanitizedValue);
    }

    public function testConvertToUtf8()
    {
        $value = 'Test String';
        $sanitizedValue = $this->getFromProtectedMethod('convertToUtf8', [$value]);
        $this->assertEquals('Test String', $sanitizedValue);
    }

    public function testConvertToUtf8HandlesNull()
    {
        $value = null;
        $sanitizedValue = $this->getFromProtectedMethod('convertToUtf8', [$value]);
        $this->assertNull($sanitizedValue);
    }

    public function testHandleMissingValues()
    {
        $header = ['Product Code', 'Product Name', 'Stock'];
        $rowData = ['123', '', ''];
        $processedRow = $this->getFromProtectedMethod('handleMissingValues', [$header, $rowData]);

        $expected = [
            'Product Code' => '123',
            'Product Name' => 'No Name',
            'Stock' => 0
        ];

        $this->assertEquals($expected, $processedRow);
    }

    public function testHandleMissingValuesWithDefaults()
    {
        $header = ['Product Code', 'Product Name'];
        $rowData = ['', 'Test Product'];
        $processedRow = $this->getFromProtectedMethod('handleMissingValues', [$header, $rowData]);

        $expected = [
            'Product Code' => 'UNKNOWN',
            'Product Name' => 'Test Product'
        ];

        $this->assertEquals($expected, $processedRow);
    }

    private function getFromProtectedMethod($methodName, $value)
    {
        $method = new ReflectionMethod(ValueHandler::class, $methodName);
        $method->setAccessible(true);

        return $method->invoke($this->valueHandler, ...$value);
    }
}
