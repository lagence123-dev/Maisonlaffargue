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

{if isset($productDetails)}
	<div class="home-product-container wk_pro-category">
		<h3 class="page-product-heading">
			{if ($productDetails)}
				{l s='Giftcard available for these products' mod='wkgiftcard'}
			{else}
				{l s='Giftcard available for all products' mod='wkgiftcard'}
			{/if}
		</h3>
		<section class="featured-products clearfix">
			<div class="products">
				{foreach from=$productDetails item="product"}
					{include file="catalog/_partials/miniatures/product.tpl" product=$product productClasses="col-xs-6 col-lg-4 col-xl-3"}
				{/foreach}
			</div>
		</section>
	</div>
{/if}
