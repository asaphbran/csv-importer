<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Interface\ImporterInterface;
use App\Validator\ProductValidator;

class ProductImporter implements ImporterInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ProductRepository $productRepository,
        private ProductValidator $productValidator,
    ) {}

    public function import(array $products, bool $testMode = false): array
    {
        $processed = $successful = $skipped = 0;
        $seenProductCodes = [];
        $validationErrors = [];

        // Fetch existing product SKUs from the database
        $existingSkuSet = $this->productRepository->findAllBySku();

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

            // Track processed Product Code to avoid duplicates within CSV
            $seenProductCodes[$data['Product Code']] = true;

            // Create new Product entity
            $product = new Product();
            $product->setSku($data['Product Code']);
            $product->setName($data['Product Name']);
            $product->setDescription($data['Product Description'] ?? "");
            $product->setCostInGbp($data['Cost in GBP']);
            $product->setStock((int) $data['Stock']);
            $product->setTimestamp();
            $product->setAddedAt(new \DateTime());

            // Handle discontinued products
            if (!empty($data['Discontinued']) && strtolower($data['Discontinued']) === 'yes') {
                $product->setDiscontinuedAt(new \DateTime());
            }

            // Validate the product using ProductValidator
            $errors = $this->productValidator->validate($product);

            if (!empty($errors)) {
                $skipped++;
                $validationErrors[$data['Product Code']] = $errors;
                continue;
            }

            // If test mode is true, won't persist
            if (!$testMode) {
                $this->entityManager->persist($product);
            }

            $successful++;
        }

        // If test mode is true, won't flush, which means that won't insert into the database
        if (!$testMode) {
            $this->entityManager->flush();
        }

        return [$processed, $successful, $skipped, $validationErrors];
    }
}
