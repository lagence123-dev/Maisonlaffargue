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
<li class="tree-item{if isset($node['disabled']) && $node['disabled'] == true} tree-item-disable{/if}">
	<span class="tree-item-name{if isset($node['disabled']) && $node['disabled'] == true} tree-item-name-disable{/if}">
		<input type="checkbox" name="{$input_name}[]" value="{$node['id_category']}"{if isset($node['disabled']) && $node['disabled'] == true} disabled="disabled"{/if} />
		<i class="tree-dot"></i>
		<label class="tree-toggler">{$node['name']}</label>
	</span>
</li>