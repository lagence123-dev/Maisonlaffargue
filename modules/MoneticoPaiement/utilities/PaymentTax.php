<?php

namespace MoneticoPaiement\utilities;

use Cart;
use Exception;
use MoneticoPaiement\Model\MoneticoPaiementTransaction;

trait PaymentTax {

    /**
     * Return the total and if the order was taxed.
     *
     * @param Cart $cart
     * @param MoneticoPaiementTransaction|null $transaction
     * @return array
     * @throws Exception
     */
    public function getOrderTotalAndTaxStatus(Cart $cart, MoneticoPaiementTransaction $transaction = null): array
    {
        $hasTaxes = $this->isOrderTaxed($cart, $transaction);
        $total = $cart->getOrderTotal($hasTaxes);

        return [$total, $hasTaxes];
    }

    /**
     * Determine if the order is taxed based on various factors.
     *
     * This method checks the tax status of an order by considering:
     * 1. The transaction details, if available.
     * 2. The amounts with and without taxes.
     *
     * @param Cart $cart
     * @param MoneticoPaiementTransaction|null $transaction
     * @return bool
     */
    private function isOrderTaxed(Cart $cart, ?MoneticoPaiementTransaction $transaction): bool
    {
        $totalWithTaxes = $cart->getOrderTotal();
        $totalWithoutTaxes = $cart->getOrderTotal(false);

        // Try to get the data from the transaction - otherwise true by default
        if ($transaction === null) {
            $hasTaxes = true;
        } else {
            $hasTaxes = $transaction->hasTaxes();
        }

        // If taxes are enabled, last check with amounts (might be affected by another module handling VAT)
        if ($hasTaxes) {
            $hasTaxes = $totalWithTaxes !== $totalWithoutTaxes;
        }

        return $hasTaxes;
    }
}