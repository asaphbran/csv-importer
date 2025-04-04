<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: "tblProductData")]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "intProductDataId", type: "integer", options: ["unsigned" => true])]
    private ?int $id = null;

    #[ORM\Column(name: "strProductName", type: "string", length: 50)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 50)]
    private string $name;

    #[ORM\Column(name: "strProductDesc", type: "string", length: 255, options: ["default" => "No description"])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private string $description;

    #[ORM\Column(name: "strProductCode", type: "string", length: 10, unique: true)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 10)]
    private string $sku;

    #[ORM\Column(name: "decCostInGBP", type: "decimal", precision: 10, scale: 2)]
    #[Assert\NotBlank]
    #[Assert\Range(min: 5, max: 1000, notInRangeMessage: "The product cost must be between £5 and £1000.")]
    private float $costInGbp;

    #[ORM\Column(name: "intStock", type: "integer", options: ["default" => 0])]
    #[Assert\NotBlank]
    #[Assert\PositiveOrZero]
    #[Assert\GreaterThanOrEqual(10, message: "Stock must be at least 10.")]
    private int $stock = 10;

    #[ORM\Column(name: "dtmAdded", type: "datetime", nullable: true)]
    private ?\DateTime $addedAt = null;

    #[ORM\Column(name: "dtmDiscontinued", type: "datetime", nullable: true)]
    private ?\DateTime $discontinuedAt = null;

    #[ORM\Column(name: "stmTimestamp", type: "datetime", options: ["default" => "CURRENT_TIMESTAMP", "onUpdate" => "CURRENT_TIMESTAMP"])]
    private \DateTime $timestamp;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getSku(): string
    {
        return $this->sku;
    }

    public function setSku(string $sku): void
    {
        $this->sku = $sku;
    }

    public function getCostInGbp(): float
    {
        return $this->costInGbp;
    }

    public function setCostInGbp(float $costInGbp): void
    {
        $this->costInGbp = $costInGbp;
    }

    public function setStock(int $stock): void
    {
        $this->stock = $stock;
    }
    
    public function getStock(): int
    {
        return $this->stock;
    }

    public function getAddedAt(): ?\DateTime
    {
        return $this->addedAt;
    }

    public function setAddedAt(?\DateTime $addedAt): void
    {
        $this->addedAt = $addedAt;
    }

    public function getDiscontinuedAt(): ?\DateTime
    {
        return $this->discontinuedAt;
    }

    public function setDiscontinuedAt(?\DateTime $discontinuedAt): void
    {
        $this->discontinuedAt = $discontinuedAt;
    }

    public function getTimestamp(): \DateTime
    {
        return $this->timestamp;
    }

    public function setTimestamp(): void
    {
        $this->timestamp = new \DateTime();
    }
}
