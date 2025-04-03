<?php

namespace App\Service;

class ValueHandler
{
    protected function sanitizeValue(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        // Trim spaces and remove currency symbols
        $value = trim($value);
        $value = preg_replace('/[\p{Sc}]/u', '', $value);

        return $value;
    }


    protected function convertToUtf8(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        return mb_convert_encoding($value, 'UTF-8', 'auto');
    }

    protected function handleMissingValues(array $header, array $rowData): array
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
            $processedRow[$key] = !empty($rowData[$index]) ? $rowData[$index] : ($defaults[$key] ?? null);
        }

        return $processedRow;
    }
}