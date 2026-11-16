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
<a href="{$link}"{if isset($action)} onclick="{$action}"{/if}{if isset($id)} id="{$id|escape:'html':'UTF-8'}"{/if} class="btn btn-default">
	{if isset($icon_class)}<i class="{$icon_class}"></i>{/if}
	{l s=$label mod='blockblog'}
</a>