{*
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */
*}

{extends file="helpers/form/form.tpl"}



{block name="input_row"}

    {if $input.type == 'cms_blocks_custom'}

    {assign var=cms_loyaty value=$input.values}

        {assign var=used_points value=$input.values['used_points']}
        {assign var=not_used_points value=$input.values['not_used_points']}

        <div class="row">
            <div class="col-lg-12">
                <div class="panel">
                    <table class="table tableDnD cms" id="cms_block_{$key|escape:'htmlall':'UTF-8'}">
                        <thead>
                        <tr class="nodrag nodrop">
                            <th><b>{l s='Action' mod='blockblog'}</b></th>
                            <th><b>{l s='Date' mod='blockblog'}</b></th>
                            <th><b>{l s='Points' mod='blockblog'}</b></th>
                            <th><b>{l s='Points status' mod='blockblog'}</b></th>
                        </tr>
                        </thead>
                        <tbody>
                        {foreach $cms_loyaty.data as $key => $item}

                            <tr class="{if $key%2}alt_row{else}not_alt_row{/if} row_hover">

                                <td>
                                    {$item['name_action']|escape:'htmlall':'UTF-8'}

                                    {if $item['id_order'] != 0}
                                        (<b style="color:orange">{l s='Order id:' mod='blockblog'} #{$item['id_order']|escape:'htmlall':'UTF-8'}</b>)
                                    {/if}



                                </td>
                                <td>{$item['date_add']|escape:'htmlall':'UTF-8'}</td>
                                <td>
                                    {if $item['is_use'] == 1}

                                    <span class="label-tooltip" data-original-title="{l s='Used points' mod='blockblog'}" data-toggle="tooltip"><span class="badge badge-success">{$item['points']|escape:'htmlall':'UTF-8'}</span></span>
                                    {else}

                                    <span class="label-tooltip" data-original-title="{l s='Not used points' mod='blockblog'}" data-toggle="tooltip"><span class="badge badge-warning">{$item['points']|escape:'htmlall':'UTF-8'}</span></span>
                                    {/if}

                                </td>
                                <td>
                                    {if $item['is_use'] == 1}
                                        <b style="color:green">{l s='used' mod='blockblog'}</b>
                                    {else}
                                        <b style="color:orange">{l s='not used' mod='blockblog'}</b>
                                    {/if}
                                </td>

                            </tr>

                        {/foreach}
                        <tr>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>
                                <span class="label-tooltip" data-original-title="{l s='Not used points' mod='blockblog'}" data-toggle="tooltip"><span class="badge badge-warning">{$not_used_points|escape:'htmlall':'UTF-8'}</span></span> / <span class="label-tooltip" data-original-title="{l s='Used points' mod='blockblog'}" data-toggle="tooltip"><span class="badge badge-success">{$used_points|escape:'htmlall':'UTF-8'}</span></span></td>
                            <td>&nbsp;</td>
                        </tr>
                        </tbody>
                    </table>

                    <div class="text-align-center">
                        <ul class="pagination">
                            {$input.paging nofilter}
                        </ul>
                    </div>
                    <div class="clear"></div>

                </div>

            </div>
        </div>


    {else}
        {$smarty.block.parent}
    {/if}
{/block}
