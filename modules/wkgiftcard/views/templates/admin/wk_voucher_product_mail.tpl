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
{foreach $list as $code}
	{foreach $code as $voucherCode => $productList}
		<div style="font-family:Open sans, arial, sans-serif;font-size:16px;font-weight:600;line-height:25px;text-align:left;color:#363A41;" align="left">{l s='You can avail discount on below products with voucher code ' mod='wkgiftcard'}{$voucherCode|escape:'html':'UTF-8'}</div>
		<div style="font-family:Open sans, arial, sans-serif;font-size:14px;line-height:25px;text-align:left;">
			<ul>
				{foreach $productList as $product}
					<li>
						<a href="{$product['url']|escape:'html':'UTF-8'}">
							{$product['name']|escape:'html':'UTF-8'}
						</a>
					</li>
				{/foreach}
			</ul>
		</div>
	{/foreach}
{/foreach}
