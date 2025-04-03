<?php

namespace App\Tests\Service;

use App\Service\CsvImporter;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use PHPUnit\Framework\TestCase;

class CsvImporterTest extends TestCase
{
    private CsvImporter $csvImporter;

    protected function setUp(): void
    {
        // CsvImporter extends ValueHandler, so we don't pass ValueHandler to the constructor
        $this->csvImporter = new CsvImporter();
    }

    public function testParseCsvSuccessfully(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Simulate a valid CSV file
        $sheet->fromArray([
            ['Product Code', 'Product Name', 'Cost in GBP'],  // Header
            ['P001', 'Laptop', '£599.99'],
            ['P002', 'Monitor', '€199.99'],
            ['P003', 'Mouse', '10.50'],
        ]);

        // Save to temp CSV file
        $filePath = tempnam(sys_get_temp_dir(), 'csv');
        $writer = new Csv($spreadsheet);
        
        // Ensure CSV settings match what we set in CsvImporter
        $writer->setDelimiter(',');
        $writer->setEnclosure('"');
        $writer->save($filePath);

        // Run parseCsv() on the temp file
        $result = $this->csvImporter->import(['filePath' => $filePath]);
        unlink($filePath); // Clean up

        // Correct expectations
        $this->assertCount(3, $result);
        $this->assertSame(['Product Code' => 'P001', 'Product Name' => 'Laptop', 'Cost in GBP' => '599.99'], $result[0]);
        $this->assertSame(['Product Code' => 'P002', 'Product Name' => 'Monitor', 'Cost in GBP' => '199.99'], $result[1]);
        $this->assertSame(['Product Code' => 'P003', 'Product Name' => 'Mouse', 'Cost in GBP' => '10.5'], $result[2]);
    }
}
