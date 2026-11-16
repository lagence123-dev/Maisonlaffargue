<?php

namespace MoneticoPaiement\Sdk\Utilities\Models\OrderContext;

class ShoppingCart implements \JsonSerializable
{
    /**
     * @var ?int
     */
    private ?int $giftCardAmount = null;

    /**
     * @var ?int
     */
    private ?int $giftCardCount = null;

    /**
     * @var ?string
     */
    private ?string $giftCardCurrency = null;

    /**
     * @var ?\DateTime
     */
    private ?\DateTime $preOrderDate = null;

    /**
     * @var ?bool
     */
    private ?bool $preorderIndicator = null;

    /**
     * @var ?bool
     */
    private ?bool $reorderIndicator = null;

    /**
     * @var ?ShoppingCartItems[]
     */
    private ?array $shoppingCartItems = null;

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
     * @return ?int
     */
    public function getGiftCardAmount(): ?int
    {
        return $this->giftCardAmount;
    }

    /**
     * @param ?int $giftCardAmount
     */
    public function setGiftCardAmount(?int $giftCardAmount): void
    {
        $this->giftCardAmount = mb_substr($giftCardAmount, 0, 12);
    }

    /**
     * @return ?int
     */
    public function getGiftCardCount(): ?int
    {
        return $this->giftCardCount;
    }

    /**
     * @param ?int $giftCardCount
     */
    public function setGiftCardCount(?int $giftCardCount): void
    {
        if ($giftCardCount > 0 && $giftCardCount < 100) {
            $this->giftCardCount = $giftCardCount;
        }
    }

    /**
     * @return ?string
     */
    public function getGiftCardCurrency(): ?string
    {
        return $this->giftCardCurrency;
    }

    /**
     * @param ?string $giftCardCurrency
     */
    public function setGiftCardCurrency(?string $giftCardCurrency): void
    {
        if (mb_strlen($giftCardCurrency) === 3) {
            $this->giftCardCurrency = $giftCardCurrency;
        }
    }

    /**
     * @return ?\DateTime
     */
    public function getPreOrderDate(): ?\DateTime
    {
        return $this->preOrderDate;
    }

    /**
     * @return ?string
     */
    public function getFormatedPreOrderDate(): ?string
    {
        if ($this->getPreOrderDate() instanceof \DateTime) {
            return $this->getPreOrderDate()->format("Y-m-d");
        }
        return null;
    }

    /**
     * @param ?\DateTime $preOrderDate
     */
    public function setPreOrderDate(?\DateTime $preOrderDate): void
    {
        $this->preOrderDate = $preOrderDate;
    }

    /**
     * @return ?bool
     */
    public function getPreorderIndicator(): ?bool
    {
        return $this->preorderIndicator;
    }

    /**
     * @param ?bool $preorderIndicator
     */
    public function setPreorderIndicator(?bool $preorderIndicator): void
    {
        $this->preorderIndicator = $preorderIndicator;
    }

    /**
     * @return ?bool
     */
    public function getReorderIndicator(): ?bool
    {
        return $this->reorderIndicator;
    }

    /**
     * @param ?bool $reorderIndicator
     */
    public function setReorderIndicator(?bool $reorderIndicator): void
    {
        $this->reorderIndicator = $reorderIndicator;
    }

    /**
     * @return \Monetico\Sdk\Request\CustomerShoppingCartItems[]|null
     */
    public function getShoppingCartItems(): ?array
    {
        return $this->shoppingCartItems;
    }

    /**
     * @param \Monetico\Sdk\Request\CustomerShoppingCartItems[]|null $shoppingCartItems
     */
    public function setShoppingCartItems(?array $shoppingCartItems): void
    {
        $this->shoppingCartItems = $shoppingCartItems;
    }

}