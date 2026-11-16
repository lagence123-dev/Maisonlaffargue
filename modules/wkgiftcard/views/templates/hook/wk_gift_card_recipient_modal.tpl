{*
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


{if isset($recipient_details) && !empty($recipient_details)}
    {assign var="modal" value="product-customizations-modal-{$idCustomization}"}
    <style>
        .wk_word_wrap {
            word-wrap: break-word !important;
        }

        .wk_text_transformation {
            text-transform: lowercase !important;
        }

        .wk_text_transformation::first-letter {
            text-transform: uppercase !important;
        }

        [data-target*="#{$modal}"] {
            display: none !important;

        }
    </style>

    {if isset($wk_option_page) && $wk_option_page == 'orderconfirmation' || $wk_option_page == 'admincart'}
        <br>
    {/if}

    <button type="button"
        class="btn btn-primary mt-2 wk_text_transformation {if  $wk_option_page == 'AdminOrders'} wk_custom_btn{/if}"
        href="#" data-toggle="modal"
        data-target="#product-options-modal-{$option_model_key|escape:'htmlall':'UTF-8'}">{l s='Recipient details' mod='wkgiftcard'}
    </button>

    <div class="modal fade" id="product-options-modal-{$option_model_key|escape:'htmlall':'UTF-8'}" tabindex="-1"
        role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" style="display:contents">
                        {l s='Recipient details' mod='wkgiftcard'}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="{l s='Close' mod='wkgiftcard'}">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div>
                        <span>
                            <strong>{l s='Recipient name' mod='wkgiftcard'}:</strong>
                            {$recipient_details.recipient_name|escape:'htmlall':'UTF-8'}
                        </span>
                    </div>
                    <br />
                    <div>
                        <span>
                            <strong>{l s='Recipient email' mod='wkgiftcard'}:</strong>
                            {$recipient_details.recipient_email|escape:'htmlall':'UTF-8'}
                        </span>
                    </div>
                    <br />
                    {if (isset($wk_show_delivery_date) && $wk_show_delivery_date == 1) && (!empty($recipient_details['delivery_date']))}
                        <div>
                            <span>
                                <strong> {l s='Delivery date' mod='wkgiftcard'}: </strong>
                                {Tools::displayDate($recipient_details.delivery_date)|escape:'html':'UTF-8'}
                            </span>
                        </div>
                        <br />
                    {/if}
                    {if !empty($recipient_details.message)}
                        <div>
                            <span class='wk_word_wrap'>
                                <strong>{l s='Message' mod='wkgiftcard'}:</strong>
                                {$recipient_details.message|escape:'htmlall':'UTF-8'}
                            </span>
                        </div>
                        <br />
                    {/if}
                </div>
            </div>
        </div>
    </div>
{/if}