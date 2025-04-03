<?php

namespace App\Tests\Service;

use App\Entity\Product;
use App\Service\ProductImporter;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class ProductImporterTest extends TestCase
{
    private MockObject $entityManager;
    private ProductImporter $productImporter;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->productImporter = new ProductImporter($this->entityManager);
    }

    public function testImportSkipsLowStockLowPrice(): void
    {
        $products = [['Product Code' => 'P001', 'Product Name' => 'Item1', 'Cost in GBP' => 4, 'Stock' => 5, 'Discontinued' => 'no']];
        [$processed, $successful, $skipped] = $this->productImporter->import($products, true);

        $this->assertEquals(1, $processed);
        $this->assertEquals(0, $successful);
        $this->assertEquals(1, $skipped);
    }

    public function testImportSkipsHighCostItems(): void
    {
        $products = [['Product Code' => 'P002', 'Product Name' => 'ExpensiveItem', 'Cost in GBP' => 1500, 'Stock' => 10, 'Discontinued' => 'no']];
        [$processed, $successful, $skipped] = $this->productImporter->import($products, true);

        $this->assertEquals(1, $processed);
        $this->assertEquals(0, $successful);
        $this->assertEquals(1, $skipped);
    }

    public function testImportHandlesDiscontinuedItems(): void
    {
        $products = [['Product Code' => 'P003', 'Product Name' => 'OldItem', 'Cost in GBP' => 20, 'Stock' => 30, 'Discontinued' => 'yes']];
        [$processed, $successful, $skipped] = $this->productImporter->import($products, true);

        $this->assertEquals(1, $processed);
        $this->assertEquals(1, $successful);
        $this->assertEquals(0, $skipped);
    }

    public function testImportPersistsValidProducts(): void
    {
        $products = [['Product Code' => 'P004', 'Product Name' => 'ValidItem', 'Cost in GBP' => 50, 'Stock' => 20, 'Discontinued' => 'no']];

        $this->entityManager->expects($this->once())->method('persist');
        $this->entityManager->expects($this->once())->method('flush');

        [$processed, $successful, $skipped] = $this->productImporter->import($products, false);

        $this->assertEquals(1, $processed);
        $this->assertEquals(1, $successful);
        $this->assertEquals(0, $skipped);
    }
}
