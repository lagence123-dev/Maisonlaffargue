<?php

namespace MoneticoPaiement\Sdk\Api\PaymentService\Payment;

class PreauthorisationPayment
{
    /** @var string $invoice_type */
    private string $invoice_type;

    /** @var string $file_number */
    private string $file_number;

    /**
     * @return array
     */
    public function toArray():array
    {
        return array_filter([
            'invoice_type' => $this->getInvoiceType(),
            'file_number' => $this->getFileNumber()
        ], function ($value) {
            return !is_null($value);
        });
    }

    public function getInvoiceType(): string
    {
        return $this->invoice_type;
    }

    /**
     * @param string $invoice_type
     *
     * @return void
     */
    public function setInvoiceType(string $invoice_type): void
    {
        $this->invoice_type = $invoice_type;
    }

    /**
     * @return string
     */
    public function getFileNumber(): string
    {
        return $this->file_number;
    }

    /**
     * @param string $file_number
     *
     * @return void
     */
    public function setFileNumber(string $file_number): void
    {
        $this->file_number = $file_number;
    }
}