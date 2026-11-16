{**
* 2011 - 2020 StorePrestaModules SPM LLC.
*
* MODULE blockblog
*
* @author    SPM <spm.presto@gmail.com>
* @copyright Copyright (c) permanent, SPM
* @license   Addons PrestaShop license limitation
* @version   2.4.3
* @link      http://addons.prestashop.com/en/2_community-developer?contributor=790166
*
* NOTICE OF LICENSE
*
* Don't use this module on several shops. The license provided by PrestaShop Addons
* for all its modules is valid only once for a single shop.
*
*}

{if $blockblogloyality_onl == 1}
    {if $blockblogloyality_order_statusl == "loyality_order"}
        {if $blockblogloyality_order_timesul <= $blockblogloyality_order_timesl}

            <p class="blockblog-loyalty">
                <br/>
                <img src="{$base_dir_ssl|escape:'htmlall':'UTF-8'}modules/blockblog/views/img/flag.png"
                     alt ="{$blockblogloyality_registration_pointsl|escape:'htmlall':'UTF-8'}"
                        />&nbsp;{$blockblogloyality_order_descriptionl|replace:'[points]':$blockblogloyality_order_pointsl nofilter}
            </p>
        {/if}
    {/if}
{/if}