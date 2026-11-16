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

{foreach from=$blockblogmy_data item=item}
    <tr class="pl-animater {$blockblogd_eff_loyalty_my|escape:'htmlall':'UTF-8'}">
        <td class="center" style="width:55%">
            {$item['name_action'] nofilter}

            {if $item['id_order'] != 0}
                {*(<b style="color:orange">{l s='Order id:' mod='blockblog'} #{$item['id_order']|escape:'htmlall':'UTF-8'}</b>)*}
                (<b style="color:orange">{l s='Order' mod='blockblog'}</b>)
            {/if}


        </td>
        <td class="center">

            {$item['date_add']|escape:'htmlall':'UTF-8'}
        </td>
        <td class="center">
            {if $item['is_use'] == 1}

                <span class="label-tooltip" data-original-title="{l s='Used points' mod='blockblog'}" data-toggle="tooltip"><span class="badge badge-success">{$item['points']|escape:'htmlall':'UTF-8'}</span></span>
            {else}

                <span class="label-tooltip" data-original-title="{l s='Not used points' mod='blockblog'}" data-toggle="tooltip"><span class="badge badge-warning">{$item['points']|escape:'htmlall':'UTF-8'}</span></span>
            {/if}


        </td>
        <td class="center">
            {if $item['is_use'] == 1}
                <b style="color:green">{l s='used' mod='blockblog'}</b>
            {else}
                <b style="color:orange">{l s='not used' mod='blockblog'}</b>
            {/if}


        </td>

    </tr>
{/foreach}
<tr>
    <td colspan = 2 align = right>
        <span class="label-tooltip" data-original-title="{l s='Not used points' mod='blockblog'}" data-toggle="tooltip"><span class="badge badge-warning">{l s='Not used points' mod='blockblog'}</span></span> / <span class="label-tooltip" data-original-title="{l s='Used points' mod='blockblog'}" data-toggle="tooltip"><span class="badge badge-success">{l s='Used points' mod='blockblog'}</span></span>

    </td>
    <td>
        <span class="label-tooltip" data-original-title="{l s='Not used points' mod='blockblog'}" data-toggle="tooltip"><span class="badge badge-warning">{$not_used_points|escape:'htmlall':'UTF-8'}</span></span> / <span class="label-tooltip" data-original-title="{l s='Used points' mod='blockblog'}" data-toggle="tooltip"><span class="badge badge-success">{$used_points|escape:'htmlall':'UTF-8'}</span></span></td>
    <td>&nbsp;</td>
</tr>


{if $blockblogd_eff_loyalty_my != "disable_all_effects"}
{literal}
    <script type="text/javascript">
        document.addEventListener("DOMContentLoaded", function(event) {
            $(document).ready(function(){
                blockblog_init_effects_loyalty();
            });
        });
    </script>
{/literal}
{/if}