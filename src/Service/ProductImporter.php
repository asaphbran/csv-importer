<?php

namespace App\Service;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use App\Interface\ImporterInterface;

class ProductImporter implements ImporterInterface
{
    private float $minCost;
    private int $minStock;
    private float $maxCost;

    public function __construct(
        private EntityManagerInterface $entityManager,
        float $minCost = 5.0,
        int $minStock = 10,
        float $maxCost = 1000.0
    ) {
        $this->minCost = $minCost;
        $this->minStock = $minStock;
        $this->maxCost = $maxCost;
    }

    public function import(array $products, bool $testMode = false): array
    {
        $processed = $successful = $skipped = 0;
        $seenProductCodes = [];

        // Fetch existing product SKUs from the database
        $existingProducts = $this->entityManager->getRepository(Product::class)
            ->createQueryBuilder('p')
            ->select('p.sku')
            ->getQuery()
            ->getResult();
        
        $existingSkuSet = $existingProducts ? array_flip(array_column($existingProducts, 'sku')) : [];

        foreach ($products as $data) {
            $processed++;

            // Skip if Product Code is missing
            if (empty($data['Product Code'])) {
                $skipped++;
                continue;
            }

            // Skip duplicate Product Codes (either from CSV itself or already in DB)
            if (isset($seenProductCodes[$data['Product Code']]) || isset($existingSkuSet[$data['Product Code']])) {
                $skipped++;
                continue;
            }

            // Business rules: skip based on cost & stock conditions
            if ($data['Cost in GBP'] < $this->minCost && $data['Stock'] < $this->minStock) {
                $skipped++;
                continue;
            }
            if ($data['Cost in GBP'] > $this->maxCost) {
                $skipped++;
                continue;
            }

            // Track processed Product Code to avoid duplicates within CSV
            $seenProductCodes[$data['Product Code']] = true;

            // Create new Product entity
            $product = new Product();
            $product->setSku($data['Product Code']);
            $product->setName($data['Product Name']);
            $product->setDescription($data['Product Description'] ?? 'No Description');
            $product->setTimestamp();
            $product->setAddedAt(new \DateTime());

            // Handle discontinued products
            if (!empty($data['Discontinued']) && strtolower($data['Discontinued']) === 'yes') {
                $product->setDiscontinuedAt(new \DateTime());
            }

            if (!$testMode) {
                $this->entityManager->persist($product);
            }

            $successful++;
        }

        if (!$testMode) {
            $this->entityManager->flush();
        }

        return [$processed, $successful, $skipped];
    }
}
