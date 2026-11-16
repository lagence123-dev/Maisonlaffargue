<?php
/**
 *   Copyright since 2009 ohmyweb!
 *
 *   @author    ohmyweb <contact@ohmyweb.fr>
 *   @copyright since 2009 ohmyweb!
 *   @license   Proprietary - no redistribution without authorization
 */

namespace DwfProductExtraFields\Twig;

if (!defined('_PS_VERSION_')) {
    exit;
}

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Currency;
use Context;
use Tools;

class PriceExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('convertPrice', [$this, 'convertPrice']),
        ];
    }

    public function convertPrice(float $price): float
    {
        $currency = Context::getContext()->currency;
        return Tools::convertPrice($price, $currency);
    }
}
