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

<span>
	<span class="cus_display_flex">
		{if Configuration::get('PS_INVOICE') && $order->invoice_number}
			<a class="btn wk_cus_btn_icon  _blank"
				href="{$link->getAdminLink('AdminPdf')|escape:'html':'UTF-8'}&amp;submitAction=generateInvoicePDF&amp;id_order={$order->id|escape:'html':'UTF-8'}"
				data-toggle="tooltip" data-placement="top" title="{l s='View invoice' mod='wkgiftcard'}">
				<i class="icon-file-text wk_cus_icon_font_size"></i>
			</a>
		{/if}

		{if $order->delivery_number}
			<a class="btn wk_cus_btn_icon _blank"
				href="{$link->getAdminLink('AdminPdf')|escape:'html':'UTF-8'}&amp;submitAction=generateDeliverySlipPDF&amp;id_order={$order->id|escape:'html':'UTF-8'}"
				data-toggle="tooltip" data-placement="top" title="{l s='View delivery slip' mod='wkgiftcard'}">
				<i class="icon-truck wk_cus_icon_font_size"></i>
			</a>
		{/if}
	</span>
</span>