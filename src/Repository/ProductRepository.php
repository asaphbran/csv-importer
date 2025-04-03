<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    /**
     * Fetch all existing product SKUs as an associative array for fast lookup.
     *
     * @return array<string, true> Array where keys are SKUs and values are `true`
     */
    public function findAllBySku(): array
    {
        $existingProducts = $this->createQueryBuilder('p')
            ->select('p.sku')
            ->getQuery()
            ->getResult();

        return $existingProducts ? array_flip(array_column($existingProducts, 'sku')) : [];
    }
}
