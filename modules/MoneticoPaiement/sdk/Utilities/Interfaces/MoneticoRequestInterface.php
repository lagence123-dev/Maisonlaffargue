<?php

namespace MoneticoPaiement\Sdk\Utilities\Interfaces;

// Declare the interface 'Template'
interface MoneticoRequestInterface
{
    public function getFields():array;
    public function getUrl():string;
}