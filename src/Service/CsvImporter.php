<?php

namespace App\Service;

use PhpOffice\PhpSpreadsheet\Reader\Csv;
use App\Service\ValueHandler;
use App\Interface\ImporterInterface;

class CsvImporter extends ValueHandler implements ImporterInterface
{
    public function import(array $data, bool $testMode = false): array
    {
        // Before going in to import the data, we make sure that the file exists and is readable 
        if (empty($data['filePath']) || !file_exists($data['filePath']) || !is_readable($data['filePath'])) {
            throw new \Exception("Unable to read the CSV file: " . ($data['filePath'] ?? 'Unknown'));
        }

        $reader = new Csv();
        $reader->setDelimiter(',');
        $reader->setEnclosure('"'); // Handles quoted values
        $reader->setSheetIndex(0);  // Ensure the first sheet is read
        $reader->setInputEncoding('UTF-8'); // Ensure correct encoding

        $spreadsheet = $reader->load($data['filePath']);
        $worksheet = $spreadsheet->getActiveSheet();

        $rows = [];
        $header = [];
        $rowIndex = 0;

        foreach ($worksheet->getRowIterator() as $row) {
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false); // Ensures empty cells are included

            $rowData = [];
            foreach ($cellIterator as $cell) {
                $value = $cell->getFormattedValue();
                $value = $this->sanitizeValue($value);
                $rowData[] = $value;
            }

            // Skip empty rows (if they contain only null or empty values)
            if (empty(array_filter($rowData))) {
                continue;
            }

            // First row is the header
            if ($rowIndex === 0) {
                $header = $rowData;
            } else {
                // Ensure column count matches header before processing
                if (count($header) !== count($rowData)) {
                    continue; // Skip malformed rows
                }

                // Convert values to UTF-8
                $rowData = array_map([$this, 'convertToUtf8'], $rowData);

                // Apply missing value handling
                $rows[] = $this->handleMissingValues($header, $rowData);
            }
            
            $rowIndex++;
        }

        return $rows;
    }
}
