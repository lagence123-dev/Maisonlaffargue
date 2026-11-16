<?php

namespace MoneticoPaiement\Sdk\Response\PaymentSession;

class Error
{
    private string $type;
    private ?array $input_errors = null;
    private ?array $mac_error = null;
}