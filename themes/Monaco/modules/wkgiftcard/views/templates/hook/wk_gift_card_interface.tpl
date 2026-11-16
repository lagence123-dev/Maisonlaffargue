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
{if !$is_catalog_mode}
    {if $is_dynamic_price_gift_card == 1}
        <style>
            .product-container .product-prices {
                display: none;
            }
        </style>
    {/if}
    <style>
        #gift_product_quantity_wanted {
            width: 3rem;
            height: 2.75rem;
            padding: 0.175rem 0.5rem;
            color: #232323;
            background-color: #fff;
        }

        #gift_card_price {
            height: 2.75rem;
            background-color: #fff;
        }

        #ui-datepicker-div {
            z-index: 2000 !important;
        }

        #gift_button {
            height: 2.75rem;
            padding-top: 0.625rem;
            line-height: inherit;
        }

        @media only screen and (max-width: 912px) {
            .js-product-add-to-cart {
                margin-top: 1rem !important;
            }

            .wk-gift-card .bootstrap-touchspin {
                width: 72px;
            }
        }
        .nav-link {
            cursor: pointer;
            outline: none !important;
        }

        .nav-tabs {
            border-bottom: 0px !important;
        }

        .wk-gift-card .tab-pane {
            border: 1px solid #ddd !important;
            margin-bottom: 5px;
        }
        .product-quantity {
            display: flex;
            justify-content: space-between;
        }
        .bootstrap-touchspin .input-group-btn-vertical > .btn {
            height: 22px;
            color: #6c757d;
        }
        .bootstrap-touchspin .input-group-btn-vertical .bootstrap-touchspin-down {
            top: 22px;
        }
    </style>
    <div class="product-customization wk-gift-card">
        <div class="card card-block" style="text-align: left;">
            <div class="col-sm-12">
                <input type="hidden" id="is_dynamic_price_gift_card"
                    value="{$is_dynamic_price_gift_card|escape:'html':'UTF-8'}" />

                <div id="wkGiftCardPurchaseTabContent">
                    {* Other Purchase tab content *}
                    <div id="wkOtherGiftCardPurchase"
                        aria-labelledby="wk-other-tab">
                        <div id="wk-other-tab" class='active my-1 px-1'>
                            <div class="form-group row">
                                <div class="col-md-12">
                                    <span class="control-label"><span class="text-danger">*</span>
                                        {l s='To name' mod='wkgiftcard'}</span>
                                    <input class="form-control" id="gift_recipient_name" type="text"
                                        placeholder="{l s='Enter recipient full name' mod='wkgiftcard'}" value="" />
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-12">
                                    <span class="control-label"><span class="text-danger">*</span>
                                        {l s='To email' mod='wkgiftcard'}</span>
                                    <input class="form-control" id="gift_recipient_email" type="text"
                                        placeholder="{l s='Enter recipient email address' mod='wkgiftcard'}" value="" />
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-12">
                                    <span class="control-label"><span class="text-danger">*</span>
                                        {l s='From name' mod='wkgiftcard'}</span>
                                    <input class="form-control" id="gift_your_name" type="text"
                                        placeholder="{l s='Enter your name' mod='wkgiftcard'}"
                                        value="{$from_name|escape:'html':'UTF-8'}" />
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-12">
                                    <span class="control-label">{l s='Your message' mod='wkgiftcard'}</span>
                                    <textarea class="form-control" id="gift_recipient_message" rows="2" cols="50"
                                        placeholder="{l s='Enter your message' mod='wkgiftcard'}"></textarea>
                                </div>
                            </div>

                            {if isset($is_customer_login) && $is_customer_login}
                                <input class="form-control" id="gift_recipient_name_self" type="hidden"
                                       placeholder="{l s='Enter recipient full name' mod='wkgiftcard'}"
                                       value="{if isset($self_name)}{$self_name|escape:'html':'UTF-8'}{/if}" />
                                <input class="form-control" id="gift_recipient_email_self" type="hidden"
                                       placeholder="{l s='Enter recipient email address' mod='wkgiftcard'}"
                                       value="{if isset($self_email)}{$self_email|escape:'html':'UTF-8'}{/if}" />
                                <input class="form-control" id="gift_your_name_self" type="hidden"
                                       placeholder="{l s='Enter your name' mod='wkgiftcard'}"
                                       value="{if isset($self_name)}{$self_name|escape:'html':'UTF-8'}{/if}" />
                            {else}
                                <div class='alert alert-info'>
                                    {l s='You are currently not logged in.' mod='wkgiftcard'}
                                </div>
                            {/if}
                        </div>
                    </div>
                </div>

                {if $wk_show_delivery_date == 1}
                    <div class="form-group row mt-1">
                        <div class="col-md-12">
                            <span class="control-label">{l s='Delivery date' mod='wkgiftcard'}</span>
                            <div class="input-group">
                                <input class="form-control" id="gift_card_delivery_date" type="text"
                                    placeholder="{l s='Select voucher delivery date' mod='wkgiftcard'}" value="" readonly>
                                <span class="input-group-addon" style="width: 50px;">
                                    <i class="material-icons" style="font-size: 17px;">date_range</i>
                                </span>
                            </div>
                        </div>
                    </div>
                {/if}

                {if $is_dynamic_price_gift_card == 1}
                    <div class="form-group row">
                        <div class="col-lg-3 col-md-12">
                            <span class="control-label"><span class="text-danger">*</span> {l s='Price' mod='wkgiftcard'}</span>
                            <input class="form-control" id="gift_card_price" type="text"
                                placeholder="{l s=' Price' mod='wkgiftcard'}" value="200" />
                        </div>
                        <div class="col-lg-9 col-md-12">
                            <div class="js-product-add-to-cart">
                                <span class="control-label"><span class="text-danger">*</span>
                                    {l s='Quantity' mod='wkgiftcard'}</span>
                                <div class="product-quantity clearfix">
                                    <div class="qty">
                                        <input type="text" id="gift_product_quantity_wanted" value="1"
                                            class="input-group form-control" min="{$minimal_quantity|escape:'htmlall':'UTF-8'}"
                                            aria-label="{l s='Quantity' mod='wkgiftcard'}">
                                    </div>

                                    <div class="add">
                                        <button class="btn btn-primary" id="gift_button">
                                            <i class="material-icons shopping-cart"></i>
                                            {l s='Add to cart' mod='wkgiftcard'}
                                        </button>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                {else}
                    <div class="form-group row">
                        <div class="col-lg-12 col-md-12">
                            <div class="js-product-add-to-cart">
                                <span class="control-label">{l s='Quantity' mod='wkgiftcard'}</span>
                                <div class="product-quantity clearfix">
                                    <div class="qty">
                                        <input type="text" id="gift_product_quantity_wanted" value="1"
                                            class="input-group form-control" min="{$minimal_quantity|escape:'htmlall':'UTF-8'}"
                                            aria-label="{l s='Quantity' mod='wkgiftcard'}">
                                    </div>

                                    <div class="add">
                                        <button class="btn btn-primary" id="gift_button">
                                            <i class="material-icons shopping-cart"></i>
                                            {l s='Add to cart' mod='wkgiftcard'}
                                        </button>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                {/if}
            </div>
        {* {/if} *}
    </div>
</div>
<script>
    if (typeof addGiftProductTouchSpin !== 'undefined' && typeof addGiftProductTouchSpin === 'function') {
        addGiftProductTouchSpin();
    }
    if (typeof(document.getElementsByClassName('product-add-to-cart')[0]) != 'undefined') {
        document.getElementsByClassName('product-add-to-cart')[0].style.display = 'none';
    }
    var id_product = "{$id_product|escape:'html':'UTF-8'}";
    var is_dynamic_price_gift_card = "{$is_dynamic_price_gift_card|escape:'html':'UTF-8'}";
    if (is_dynamic_price_gift_card == 1) {
        var price_selector = document.querySelectorAll('#quickview-modal-' + id_product + '-0 .product-prices');
        if (price_selector.length > 0) {
            price_selector[0].style.display = "none";
        }
    }
    var add_to_cart_selector = document.querySelectorAll('#quickview-modal-' + id_product + '-0 .product-add-to-cart');
    if (add_to_cart_selector.length > 0) {
        add_to_cart_selector[0].style.display = "none";
    }
</script>
{/if}