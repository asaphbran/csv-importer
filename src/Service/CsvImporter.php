<?php

namespace App\Service;

use PhpOffice\PhpSpreadsheet\IOFactory;

class CsvImporter
{
    public function parseCsv(string $filePath): array
    {
        $csv = IOFactory::load($filePath);
        $worksheet = $csv->getActiveSheet();

        $rows = [];
        $header = [];

        foreach ($worksheet->getRowIterator() as $rowIndex => $row) {
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false); // Ensures empty cells are included

            $rowData = [];
            foreach ($cellIterator as $cell) {
                $value = $cell->getFormattedValue();
                $value = $this->sanitizeValue($value);
                $rowData[] = $value;
            }

            // First row is the header
            if ($rowIndex === 1) {
                $header = $rowData;
            } else {
                // Ensure column count matches header before processing
                if (count($header) !== count($rowData)) {
                    continue; // Skip malformed rows
                }

                // Convert values to UTF-8
                $rowData = array_map([$this, 'convertToUtf8'], $rowData);

                // Fill missing values with defaults, keeping the same keys as header
                $rows[] = $this->handleMissingValues($header, $rowData);
            }
        }

        return $rows;
    }

    private function sanitizeValue(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        // Trim spaces and remove currency symbols
        $value = trim($value);
        $value = preg_replace('/[\p{Sc}]/u', '', $value);

        return $value;
    }


    private function convertToUtf8(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        return mb_convert_encoding($value, 'UTF-8', 'auto');
    }

    private function handleMissingValues(array $header, array $rowData): array
    {
        // Define default values for missing data
        $defaults = [
            'Product Code' => 'UNKNOWN',
            'Product Name' => 'No Name',
            'Product Description' => 'No Description',
            'Stock' => 0,
            'Cost in GBP' => 0.0,
            'Discontinued' => 'no',
        ];

        // Merge only the keys that exist in the header
        $processedRow = [];
        foreach ($header as $index => $key) {
            $processedRow[$key] = $rowData[$index] ?? ($defaults[$key] ?? null);
        }

        return $processedRow;
    }
}
