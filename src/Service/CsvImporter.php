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
            $cellIterator->setIterateOnlyExistingCells(false);

            $rowData = [];
            foreach ($cellIterator as $cell) {
                $rowData[] = $cell->getFormattedValue();
            }

            if ($rowIndex === 1) {
                $header = $rowData;
            } else {
                if (count($header) === count($rowData)) {
                    $rows[] = array_combine($header, $rowData);
                }
            }
        }

        return $rows;
    }
}