{*
**
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

{if $blockblogislogged == 0}


{if $blockblogloyality_onl == 1}

    {if $blockblogloyality_registration_statusl == "loyality_registration"}

        {if $blockblogloyality_registration_timesul <= $blockblogloyality_registration_timesl}


        <p class="blockblog-loyalty">
            <img src="{$base_dir_ssl|escape:'htmlall':'UTF-8'}modules/blockblog/views/img/flag.png"
                 alt ="{$blockblogloyality_registration_pointsl|escape:'htmlall':'UTF-8'}"
                    />&nbsp;{$blockblogloyality_registration_descriptionl|replace:'[points]':$blockblogloyality_registration_pointsl nofilter}
        </p>
        {/if}
    {/if}
{/if}
{/if}