<?php

namespace App\Interface;

interface ImporterInterface
{
    /**
     * Process to import data from a source
     *
     * @param array $data The data to be imported
     * @param bool $testMode If true, the data is not persisted
     * @return array
     */
    public function import(array $data, bool $testMode = false): array;
}