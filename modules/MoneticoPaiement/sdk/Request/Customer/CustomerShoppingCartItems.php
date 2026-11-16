<?php

namespace MoneticoPaiement\Sdk\Request\Customer;

class CustomerShoppingCartItems implements \JsonSerializable
{

    /**
     * @var ?string
     */
    private $name;

    /**
     * @var ?string
     */
    private $description;

    /**
     * @var ?string
     */
    private $productCode;

    /**
     * @var ?string
     */
    private $imageURL;

    /**
     * @var ?int
     */
    private $unitPrice;

    /**
     * @var ?int
     */
    private $quantity;

    /**
     * @var ?string
     */
    private $productSKU;

    /**
     * @var ?string
     */
    private $productRisk;

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
     * @return string|null
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return string|null
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     */
    public function setDescription(string $description)
    {
        $this->description = mb_substr($description, 0, 2048);
    }

    /**
     * @return string|null
     */
    public function getProductCode()
    {
        return $this->productCode;
    }

    /**
     * @param string|null $productCode
     */
    public function setProductCode(string $productCode)
    {
        if (in_array($productCode, ['adult_content', 'coupon', 'default', 'electronic_good', 'electronic_software', 'gift_certificate', 'handling_only', 'service', 'shipping_and_handling', 'shipping_only', 'subscription'])) {
            $this->productCode = $productCode;
        }
    }

    /**
     * @return string|null
     */
    public function getImageURL()
    {
        return $this->imageURL;
    }

    /**
     * @param string|null $imageURL
     */
    public function setImageURL(string $imageURL)
    {
        if (mb_strlen($imageURL) <= 2000) {
            $this->imageURL = $imageURL;
        }
    }

    /**
     * @return int|null
     */
    public function getUnitPrice()
    {
        return $this->unitPrice;
    }

    /**
     * @param int $unitPrice
     */
    public function setUnitPrice(int $unitPrice)
    {
        if (strlen(strval($unitPrice)) <= 12) {
            $this->unitPrice = $unitPrice;
        }
    }

    /**
     * @return int|null
     */
    public function getQuantity(): int
    {
        return $this->quantity;
    }

    /**
     * @param int|null $quantity
     */
    public function setQuantity(int $quantity)
    {
        if ($quantity > 0) {
            $this->quantity = $quantity;
        }
    }

    /**
     * @return string|null
     */
    public function getProductSKU(): string
    {
        return $this->productSKU;
    }

    /**
     * @param string|null $productSKU
     */
    public function setProductSKU(string $productSKU)
    {
        if (mb_strlen($productSKU) <= 255) {
            $this->productSKU = $productSKU;
        }
    }

    /**
     * @return string|null
     */
    public function getProductRisk()
    {
        return $this->productRisk;
    }

    /**
     * @param string|null $productRisk
     */
    public function setProductRisk(string $productRisk)
    {
        if (in_array($productRisk, ['low', 'normal', 'high'])) {
            $this->productRisk = $productRisk;
        }
    }

}