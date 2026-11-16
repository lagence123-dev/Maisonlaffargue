<?php

namespace MoneticoPaiement\Sdk\Request\Customer;

use \MoneticoPaiement\Sdk\Request\Customer\CustomerShoppingCartItems;

class CustomerShoppingCart implements \JsonSerializable
{
    /**
     * @var ?int
     */
    private $giftCardAmount;

    /**
     * @var ?int
     */
    private $giftCardCount;

    /**
     * @var ?string
     */
    private $giftCardCurrency;

    /**
     * @var ?\DateTime?
     */
    private $preOrderDate;

    /**
     * @var ?bool
     */
    private $preorderIndicator;

    /**
     * @var ?bool
     */
    private $reorderIndicator;

    /**
     * @var ?CustomerShoppingCartItems[]
     */
    private $shoppingCartItems;

    /**
     * @return array|mixed
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return array_filter([
            "giftCardAmount" => $this->getGiftCardAmount(),
            "giftCardCount" => $this->getGiftCardCount(),
            "giftCardCurrency" => $this->getGiftCardCurrency(),
            "preOrderDate" => $this->getFormatedPreOrderDate(),
            "preorderIndicator" => $this->getPreorderIndicator(),
            "reorderIndicator" => $this->getReorderIndicator(),
            "shoppingCartItems" => $this->getShoppingCartItems()
        ], function ($value) {
            return !is_null($value);
        });
    }

    /**
     * @return int|null
     */
    public function getGiftCardAmount()
    {
        return $this->giftCardAmount;
    }

    /**
     * @param int|null $giftCardAmount
     */
    public function setGiftCardAmount(int $giftCardAmount)
    {
        $this->giftCardAmount = mb_substr($giftCardAmount, 0, 12);
    }

    /**
     * @return int|null
     */
    public function getGiftCardCount()
    {
        return $this->giftCardCount;
    }

    /**
     * @param int|null $giftCardCount
     */
    public function setGiftCardCount(int $giftCardCount)
    {
        if ($giftCardCount > 0 && $giftCardCount < 100) {
            $this->giftCardCount = $giftCardCount;
        }
    }

    /**
     * @return string|null
     */
    public function getGiftCardCurrency()
    {
        return $this->giftCardCurrency;
    }

    /**
     * @param string|null $giftCardCurrency
     */
    public function setGiftCardCurrency(string $giftCardCurrency)
    {
        if (mb_strlen($giftCardCurrency) === 3) {
            $this->giftCardCurrency = $giftCardCurrency;
        }
    }

    /**
     * @return \DateTime|null
     */
    public function getPreOrderDate()
    {
        return $this->preOrderDate;
    }

    /**
     * @return string|null
     */
    public function getFormatedPreOrderDate()
    {
        if ($this->getPreOrderDate() instanceof \DateTime) {
            return $this->getPreOrderDate()->format("Y-m-d");
        }
        return null;
    }

    /**
     * @param \DateTime|null $preOrderDate
     */
    public function setPreOrderDate(\DateTime $preOrderDate)
    {
        $this->preOrderDate = $preOrderDate;
    }

    /**
     * @return bool|null
     */
    public function getPreorderIndicator()
    {
        return $this->preorderIndicator;
    }

    /**
     * @param bool|null $preorderIndicator
     */
    public function setPreorderIndicator(bool $preorderIndicator)
    {
        $this->preorderIndicator = $preorderIndicator;
    }

    /**
     * @return bool|null
     */
    public function getReorderIndicator()
    {
        return $this->reorderIndicator;
    }

    /**
     * @param bool|null $reorderIndicator
     */
    public function setReorderIndicator(bool $reorderIndicator)
    {
        $this->reorderIndicator = $reorderIndicator;
    }

    /**
     * @return \Monetico\Sdk\Request\CustomerShoppingCartItems[]|null
     */
    public function getShoppingCartItems(): array
    {
        return $this->shoppingCartItems;
    }

    /**
     * @param \Monetico\Sdk\Request\CustomerShoppingCartItems[]|null $shoppingCartItems
     */
    public function setShoppingCartItems(array $shoppingCartItems)
    {
        $this->shoppingCartItems = $shoppingCartItems;
    }

}