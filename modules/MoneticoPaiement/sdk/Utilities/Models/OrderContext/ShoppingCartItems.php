<?php

namespace MoneticoPaiement\Sdk\Utilities\Models\OrderContext;

class ShoppingCartItems implements \JsonSerializable
{

    /**
     * @var ?string
     */
    private ?string $name = null;

    /**
     * @var ?string
     */
    private ?string $description = null;

    /**
     * @var ?string
     */
    private ?string $productCode = null;

    /**
     * @var ?string
     */
    private ?string $imageURL = null;

    /**
     * @var ?int
     */
    private ?int $unitPrice = null;

    /**
     * @var ?int
     */
    private ?int $quantity = null;

    /**
     * @var ?string
     */
    private ?string $productSKU = null;

    /**
     * @var ?string
     */
    private ?string $productRisk = null;

    /**
     * @return array|mixed
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return array_filter([
            "name" => $this->getName(),
            "description" => $this->getDescription(),
            "productCode" => $this->getProductCode(),
            "imageURL" => $this->getImageURL(),
            "unitPrice" => $this->getUnitPrice(),
            "quantity" => $this->getQuantity(),
            "productSKU" => $this->getProductSKU(),
            "productRisk" => $this->getProductRisk()
        ], function ($value) {
            return !is_null($value);
        });
    }

    /**
     * @return ?string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param ?string $name
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return ?string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param ?string $description
     */
    public function setDescription(?string $description): void
    {
        $this->description = mb_substr($description, 0, 2048);
    }

    /**
     * @return ?string
     */
    public function getProductCode(): ?string
    {
        return $this->productCode;
    }

    /**
     * @param ?string $productCode
     */
    public function setProductCode(?string $productCode): void
    {
        if (in_array($productCode, ['adult_content', 'coupon', 'default', 'electronic_good', 'electronic_software', 'gift_certificate', 'handling_only', 'service', 'shipping_and_handling', 'shipping_only', 'subscription'])) {
            $this->productCode = $productCode;
        }
    }

    /**
     * @return ?string
     */
    public function getImageURL(): ?string
    {
        return $this->imageURL;
    }

    /**
     * @param ?string $imageURL
     */
    public function setImageURL(?string $imageURL): void
    {
        if (mb_strlen($imageURL) <= 2000) {
            $this->imageURL = $imageURL;
        }
    }

    /**
     * @return ?int
     */
    public function getUnitPrice(): ?int
    {
        return $this->unitPrice;
    }

    /**
     * @param ?int $unitPrice
     */
    public function setUnitPrice(?int $unitPrice): void
    {
        if (strlen(strval($unitPrice)) <= 12) {
            $this->unitPrice = $unitPrice;
        }
    }

    /**
     * @return ?int
     */
    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    /**
     * @param ?int $quantity
     */
    public function setQuantity(?int $quantity): void
    {
        if ($quantity > 0) {
            $this->quantity = $quantity;
        }
    }

    /**
     * @return ?string
     */
    public function getProductSKU(): ?string
    {
        return $this->productSKU;
    }

    /**
     * @param ?string $productSKU
     */
    public function setProductSKU(?string $productSKU): void
    {
        if (mb_strlen($productSKU) <= 255) {
            $this->productSKU = $productSKU;
        }
    }

    /**
     * @return ?string
     */
    public function getProductRisk(): ?string
    {
        return $this->productRisk;
    }

    /**
     * @param ?string $productRisk
     */
    public function setProductRisk(?string $productRisk): void
    {
        if (in_array($productRisk, ['low', 'normal', 'high'])) {
            $this->productRisk = $productRisk;
        }
    }

}