{**
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License version 3.0
* that is bundled with this package in the file LICENSE.txt
* It is also available through the world-wide-web at this URL:
* https://opensource.org/licenses/AFL-3.0
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to a newer
* versions in the future. If you wish to customize this module for your
* needs please refer to CustomizationPolicy.txt file inside our module for more information.
*
* @author Webkul IN
* @copyright Since 2010 Webkul
* @license https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
*}

{if isset($giftCardRecipientData) && is_array($giftCardRecipientData)}
    <style>
    .wk_world_break {
        word-break:break-all;
    }

    .wk_bg_white {
        background:white;
    }

    .wk_cus_padding{
        padding:5px 10px;
    }
    </style>
    <div class='wk_bg_white wk_cus_padding'>
        <div>
            <h2> {l s='Gift card recipient details' mod='wkgiftcard'}</h2>
        </div>
        <div class='table-responsive' style='padding: 0px 7px;'>
            <table class="table table-striped wk_world_break">
                <thead>
                    <tr>
                        <th scope="col">{l s='Product name' mod='wkgiftcard'}</th>
                        <th scope="col">{l s='Recipient name' mod='wkgiftcard'}</th>
                        <th scope="col">{l s='Recipient email' mod='wkgiftcard'}</th>
                        {if isset($wk_show_delivery_date) && $wk_show_delivery_date == 1}
                            <th scope="col">{l s='Delivery date' mod='wkgiftcard'}</th>
                        {/if}
                        <th scope="col">{l s='Message' mod='wkgiftcard'}</th>
                    </tr>
                </thead>
                <tbody>
                    {foreach from=$giftCardRecipientData item=item}
                        <tr>
                            <td>{$item.product_name|escape:'htmlall':'UTF-8'}</td>
                            <td>{$item.recipient_name|escape:'htmlall':'UTF-8'}</td>
                            <td>{$item.recipient_email|escape:'htmlall':'UTF-8'}</td>
                            {if isset($wk_show_delivery_date) && $wk_show_delivery_date == 1}
                                <td>{$item.delivery_date|escape:'htmlall':'UTF-8'}</td>
                            {/if}
                            <td width='25%' >{if $item.message}{$item.message|escape:'htmlall':'UTF-8'}{else}---{/if}</td>
                        </tr>
                    {/foreach}
                </tbody>
            </table>
        </div>
    </div>
{/if}