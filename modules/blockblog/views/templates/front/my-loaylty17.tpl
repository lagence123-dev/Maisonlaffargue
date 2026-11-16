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

{extends file='page.tpl'}

{block name="page_content"}

    {if $blockblogis16 == 1 && $blockblogis17 ==0}
        {capture name=path}<a href="{$blockblogmy_a_link|escape:'htmlall':'UTF-8'}">{l s='My account' mod='blockblog'}</a>
            <span class="navigation-pipe">{$navigationPipe}</span>{l s='Blog Loyalty program' mod='blockblog'}{/capture}
    {/if}


    {if $blockblogis17 ==1}
        <a href="{$blockblogmy_a_link|escape:'htmlall':'UTF-8'}">{l s='My account' mod='blockblog'}</a>
        <span class="navigation-pipe"> > </span>{l s='Blog Loyalty program' mod='blockblog'}
    {/if}

    <h3 class="page-product-heading" id="myloyalty">{l s='My loyalty points' mod='blockblog'}</h3>





    {if count($blockblogmy_data)>0}
        <div class="block-center {if $blockblogis17 == 1}block-categories{/if}" id="block-history">
            <div id="gsniprev-list">
                <table id="block-history" class="table table-bordered responsive-table-custom">
                    <thead>
                    <tr>
                        <th class="item" style="width:200px">
                            {l s='Action' mod='blockblog'}
                        </th>
                        <th class="item">
                            {l s='Date' mod='blockblog'}
                        </th>
                        <th class="item">
                            {l s='Points' mod='blockblog'}
                        </th>
                        <th class="item">
                            {l s='Points status' mod='blockblog'}
                        </th>
                    </tr>
                    </thead>
                    <tbody id="points-list">

                    {include file="module:blockblog/views/templates/front/list_mypoints.tpl"}


                    </tbody>
                </table>

                <div id="page_nav">
                    {$blockblogpaging nofilter}
                </div>

            </div>


        </div>


    {else}
        <div class="advertise-text-review advertise-text-review-text-align">
            {l s='There are not Loyalty points yet.' mod='blockblog'}
        </div>
    {/if}


    {if $transformation_allowed}
        <br/>
        <br/>
        <p style="text-align:center; margin-top:20px">
            <a href="{$loyalty_account_url|escape:'htmlall':'UTF-8'}{$rewrite_delimeter nofilter}process=transformpoints"
               onclick="return confirm('{l s='Are you sure you want to transform your points into voucher?' mod='blockblog' js=1}');"
                    class="btn btn-success">{l s='Transform my points into a voucher of' mod='blockblog'}
                <span class="price">{$voucher}{$my_currency_iso_code}</span></a>
        </p>
        <br/>
        <br/>
    {/if}


    <br/>


    <h3 id="myvouchers" class="page-product-heading">&nbsp;{l s='My vouchers from loyalty points' mod='blockblog'}</h3>


    {if isset($discount) && count($discount) && $nbDiscounts}
        <div class="{if $blockblogis17 == 1}block-categories{/if}">
        <table class="table table-bordered table-blockblog-vouchers">
            <thead>
            <tr>
                <th class="item">{l s='Code' mod='blockblog'}</th>
                <th class="item">{l s='Description' mod='blockblog'}</th>
                <th class="item">{l s='Quantity' mod='blockblog'}</th>
                <th class="item">{l s='Value' mod='blockblog'}*</th>
                <th class="item">{l s='Minimum' mod='blockblog'}</th>
                <th class="item">{l s='Cumulative' mod='blockblog'}</th>
                <th class="item">{l s='Expiration date' mod='blockblog'}</th>
            </tr>
            </thead>
            <tbody>

            {if $blockblogis17 == 0}
                {foreach from=$discount item=discountDetail name=myLoop}
                    <tr class="{if $smarty.foreach.myLoop.first}first_item{elseif $smarty.foreach.myLoop.last}last_item{else}item{/if} {if $smarty.foreach.myLoop.index % 2}alternate_item{/if}">
                        <td class="discount_code">{$discountDetail.name|escape:'htmlall':'UTF-8'}</td>
                        <td class="discount_description">{$discountDetail.description|escape:'htmlall':'UTF-8'}</td>
                        <td class="discount_quantity">{$discountDetail.quantity_for_user|escape:'htmlall':'UTF-8'}</td>
                        <td class="discount_value">
                            {if $discountDetail.id_discount_type == 1}
                                {$discountDetail.value|escape:'htmlall':'UTF-8'}%
                            {elseif $discountDetail.id_discount_type == 2}
                                {$discountDetail.value|escape:'htmlall':'UTF-8'}
                            {else}
                                {l s='Free shipping' mod='blockblog'}
                            {/if}
                        </td>
                        <td class="discount_minimum">
                            {if $discountDetail.minimal == 0}
                                {l s='none' mod='blockblog'}
                            {else}
                                {$discountDetail.minimal|escape:'htmlall':'UTF-8'}
                            {/if}
                        </td>
                        <td class="discount_cumulative">
                            {if $discountDetail.cumulable == 1}
                                <img src="{$base_dir_ssl|escape:'htmlall':'UTF-8'}modules/blockblog/views/img/yes.gif" alt="{l s='Yes' mod='blockblog'}" class="icon" />
                            {else}
                                <img src="{$base_dir_ssl|escape:'htmlall':'UTF-8'}modules/blockblog/views/img/no.gif" alt="{l s='No' mod='blockblog'}" class="icon" />
                            {/if}
                        </td>
                        <td class="discount_expiration_date">{dateFormat date=$discountDetail.date_to}</td>
                    </tr>
                {/foreach}
            {else}

                {foreach from=$discount item=cart_rule}
                    <tr>
                        <td class="center">
                            {$cart_rule.code|escape:'htmlall':'UTF-8'}
                        </td>
                        <td class="center">
                            {$cart_rule.name|escape:'htmlall':'UTF-8'}
                        </td>
                        <td class="center">
                            {$cart_rule.quantity_for_user|escape:'htmlall':'UTF-8'}
                        </td>
                        <td class="center">
                            {$cart_rule.value|escape:'htmlall':'UTF-8'}
                        </td>
                        <td class="center">
                            {$cart_rule.voucher_minimal|escape:'htmlall':'UTF-8'}
                        </td>
                        <td class="center">
                            {$cart_rule.voucher_cumulable|escape:'htmlall':'UTF-8'}
                        </td>
                        <td class="center">
                            {$cart_rule.voucher_date|escape:'htmlall':'UTF-8'}
                        </td>
                    </tr>
                {/foreach}
            {/if}
            </tbody>
        </table>
        <p>
            *{l s='Tax included' mod='blockblog'}
        </p>
        </div>
    {else}
        <div class="advertise-text-review advertise-text-review-text-align">
            {l s='You do not possess any vouchers.' mod='blockblog'}
        </div>
    {/if}
    

    <br/>
    <ul class="footer_links clearfix">

        <li class="float-left margin-right-10">
            <a href="{$base_dir_ssl|escape:'htmlall':'UTF-8'}" class="btn btn-default button button-small-blockblog">
                <span><i class="icon-chevron-left"></i> {l s='Home' mod='blockblog'}</span>
            </a>
        </li>

        <li class="float-left">
            <a href="{$blockblogmy_a_link|escape:'htmlall':'UTF-8'}" class="btn btn-default button button-small-blockblog">
			<span>
				<i class="icon-chevron-left"></i> {l s='Back to Your Account' mod='blockblog'}
			</span>
            </a>
        </li>

    </ul>



{literal}
    <style type="text/css">
        table.std th, table.table_block th{padding:5px!important}

        @media
        only screen and (max-width: 760px),
        (min-device-width: 768px) and (max-device-width: 1024px)  {

            /* Force table to not be like tables anymore */
            table.responsive-table-custom, table.responsive-table-custom thead,
            table.responsive-table-custom tbody,
            table.responsive-table-custom th,
            table.responsive-table-custom td,
            table.responsive-table-custom tr,

            table.table-blockblog-vouchers, table.table-blockblog-vouchers thead,
            table.table-blockblog-vouchers tbody,
            table.table-blockblog-vouchers th,
            table.table-blockblog-vouchers td,
            table.table-blockblog-vouchers tr


            {
                display: block!important;
            }

            /* Hide table headers (but not display: none;, for accessibility) */
            table.responsive-table-custom thead tr, table.table-blockblog-vouchers thead tr {
                position: absolute!important;;
                top: -9999px!important;;
                left: -9999px!important;;
            }

            table.responsive-table-custom tr, table.table-blockblog-vouchers tr { border: 1px solid #ccc; }

            table.responsive-table-custom td, table.table-blockblog-vouchers td {
                /* Behave  like a "row" */
                border: none!important;;
                border-bottom: 1px solid #eee!important;;
                position: relative!important;;
                padding-left: 50%!important;;

            }

            table.responsive-table-custom td:before, table.table-blockblog-vouchers td:before {
                /* Now like a table header */
                position: absolute!important;;
                /* Top/left values mimic padding */
                top: 6px!important;;
                left: 6px!important;;
                width: 45%!important;;
                padding-right: 10px!important;;
                white-space: nowrap!important;;
            }

            /*
            Label the data
            */
            table.responsive-table-custom td:nth-of-type(1):before { content: "{/literal}{l s='Action' mod='blockblog'}{literal}"; }
            table.responsive-table-custom td:nth-of-type(2):before { content: "{/literal}{l s='Date' mod='blockblog'}{literal}"; }
            table.responsive-table-custom td:nth-of-type(3):before { content: "{/literal}{l s='Points' mod='blockblog'}{literal}"; }
            table.responsive-table-custom td:nth-of-type(4):before { content: "{/literal}{l s='Points status' mod='blockblog'}{literal}"; }

            /*
            Label the data
            */
            table.table-blockblog-vouchers td:nth-of-type(1):before { content: "{/literal}{l s='Code' mod='blockblog'}{literal}"; }
            table.table-blockblog-vouchers td:nth-of-type(2):before { content: "{/literal}{l s='Description' mod='blockblog'}{literal}"; }
            table.table-blockblog-vouchers td:nth-of-type(3):before { content: "{/literal}{l s='Quantity' mod='blockblog'}{literal}"; }
            table.table-blockblog-vouchers td:nth-of-type(4):before { content: "{/literal}{l s='Value' mod='blockblog'}{literal}?"; }
            table.table-blockblog-vouchers td:nth-of-type(5):before { content: "{/literal}{l s='Minimum' mod='blockblog'}{literal}"; }
            table.table-blockblog-vouchers td:nth-of-type(6):before { content: "{/literal}{l s='Cumulative' mod='blockblog'}{literal}?"; }
            table.table-blockblog-vouchers td:nth-of-type(7):before { content: "{/literal}{l s='Expiration date' mod='blockblog'}{literal}"; }

        }



    </style>

{/literal}

{/block}
