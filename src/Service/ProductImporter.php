<?php

namespace App\Service;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;

class ProductImporter
{
    public function __construct(private EntityManagerInterface $entityManager)
    {}

    public function import(array $products, bool $testMode = false): array
    {
        $processed = $successful = $skipped = 0;

        foreach ($products as $data) {
            $processed++;

            if ($data['Cost in GBP'] < 5 && $data['Stock'] < 10) {
                $skipped++;
                continue;
            }

            if ($data['Cost in GBP'] > 1000) {
                $skipped++;
                continue;
            }

            $product = new Product();
            $product->setSku($data['Product Code']);
            $product->setName($data['Product Name']);
            $product->setDescription($data['Product Description']);
            
            $product->setAddedAt(new \DateTime());

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
