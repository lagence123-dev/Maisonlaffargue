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

<div class="btn-group-action">
    <div class="btn-group pull-right">
        <a href="{$view_href|escape:'html':'UTF-8'}" title="{l s='view voucher' mod='wkgiftcard'}" class="edit btn btn-default">
            <i class="icon-search-plus"></i> {l s='View voucher' mod='wkgiftcard'}
        </a>
        <button class="btn btn-default dropdown-toggle" data-toggle="dropdown">
            <i class="icon-caret-down"></i>
        </button>
        <ul class="dropdown-menu">
            <li>
                <a href="{$order_link|escape:'html':'UTF-8'}" title="{l s='view order' mod='wkgiftcard'}">
                    <i class="icon-search-plus"></i> {l s='View order' mod='wkgiftcard'}
                </a>
            </li>
            {if isset($edit_href)}
                <li>
                    <a href="{$edit_href|escape:'html':'UTF-8'}" title="{l s='Send voucher mail' mod='wkgiftcard'}">
                        <i class="icon-mail-forward"></i> {l s='Send voucher mail' mod='wkgiftcard'}
                    </a>
                </li>
            {/if}
            <li></li>
        </ul>
    </div>
</div>
</td>
