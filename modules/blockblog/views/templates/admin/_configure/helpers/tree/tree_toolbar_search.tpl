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


<div class="pull-right">
	<input type="text"
		{if isset($id)}id="{$id|escape:'html':'UTF-8'}"{/if}
		{if isset($name)}name="{$name|escape:'html':'UTF-8'}"{/if}
		class="search-field{if isset($class)} {$class|escape:'html':'UTF-8'}{/if}"
		placeholder="{l s='search...' mod='blockblog'}" />
</div>

{if isset($typeahead_source) && isset($id)}

<script type="text/javascript">
	$(document).ready(
		function()
		{
			$("#{$id|escape:'html':'UTF-8'}").typeahead(
			{
				name: "{$name|escape:'html':'UTF-8'}",
				valueKey: 'name',
				local: [{$typeahead_source}]
			});

			$("#{$id|escape:'html':'UTF-8'}").keypress(function( event ) {
				if ( event.which == 13 ) {
					event.stopPropagation();
				}
			});
		}
	);
</script>
{/if}