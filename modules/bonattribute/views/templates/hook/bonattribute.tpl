{*
 * 2015-2021 Bonpresta
 *
 * Bonpresta Product Attributes and Combinations
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the General Public License (GPL 2.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/GPL-2.0
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the module to newer
 * versions in the future.
 *
 *  @author    Bonpresta
 *  @copyright 2015-2021 Bonpresta
 *  @license   http://opensource.org/licenses/GPL-2.0 General Public License (GPL 2.0)
*}

{if isset($bon_attr_list_groups)}
    <div class="bonattribute">
        <div class="bonattribute-container">
            {if $type_attr == 'radio_buttons'}
                <div class="bonattribute-box">
                    {foreach from=$bon_attr_list_groups key=id_attribute_group item=group}
                        {if $group.group_type == 'radio' || $group.group_type == 'select'}
                            <button class="bonattribute-btn {$group.group_type}{if $group.quantity =='0'} bon-attribute-disabled{/if}"
                                style="background:{$group.attribute_color}" {if $group.quantity =='0'} disabled {/if}
                                value="{$group.id_product_attribute}" data-quantity="{$group.quantity}" title="{$group.attribute_name}"
                                bon-data-id-product="{$bon_data_id_product}" bon-data-price="{$group.prices.price}"
                                {* bon-data-regular-price="{$group.prices.regular_price}" *}
                                >
                                {if $group.group_type == 'radio' || $group.group_type == 'select'} {$group.attribute_name} {/if}
                            </button>
                        {/if}
                    {/foreach}
                </div>

                <div class="bonattribute-box">
                    {foreach from=$bon_attr_list_groups key=id_attribute_group item=group}
                        {if $group.group_type == 'color'}
                            <button
                                style="{if $group.attribute_color|count_characters > 0}background: {$group.attribute_color};{else}background-image: url('{$group.texture}');{/if}"
                                class="bonattribute-btn {$group.group_type}{if $group.quantity =='0'} bon-attribute-disabled{/if}"
                                {if $group.quantity =='0'} disabled {/if} value="{$group.id_product_attribute}"
                                data-quantity="{$group.quantity}" title="{$group.attribute_name}"
                                bon-data-id-product="{$bon_data_id_product}" bon-data-price="{$group.prices.price}"
                                {* bon-data-regular-price="{$group.prices.regular_price}" *}
                                >
                            </button>
                        {/if}
                    {/foreach}
                </div>

                {if !empty($result)}
                    <div class="bonattribute-dropdown bonattribute-dropdown">
                        <div class="bonattribute-caption">{$public_group_name}</div><i class="mercury-icon-angle-bottom"></i>
                        <div class="bonattribute-list">
                            {foreach from=$result key=id_attribute_group item=group}
                                <div class="bonattribute-item bonattribute-btn {if $group.quantity =='0'} bon-attribute-disabled{/if}"
                                    {if $group.quantity =='0'} bon-data-id-product="{$bon_data_id_product}"
                                    data-quantity="{$group.quantity}" disabled {/if} value="{$group.id_product_attribute}"
                                    bon-data-price="{$group.prices.price}" 
                                    {* bon-data-regular-price="{$group.prices.regular_price}" *}
                                    >
                                    {$group.attribute_name}</div>
                            {/foreach}
                        </div>
                    </div>
                {/if}
            {/if}



            {if $type_attr == 'drop_down_list'}
                {if !empty($result)}
                    <div class="bonattribute-dropdown">
                <div class="bonattribute-caption">{if isset($public_group_name)} {$public_group_name} {/if}</div><i
                    class="mercury-icon-angle-bottom"></i>
                <div class="bonattribute-list">
                    {foreach from=$result key=id_attribute_group item=group}
                        <div class="bonattribute-item bonattribute-btn {if $group.quantity =='0'} bon-attribute-disabled{/if}"
                            {if $group.quantity =='0'} bon-data-id-product="{$bon_data_id_product}"
                            data-quantity="{$group.quantity}" disabled {/if} value="{$group.id_product_attribute}">
                            {$group.attribute_name}</div>
                    {/foreach}
                </div>
            </div>

                {/if}
            {/if}
        </div>
    </div>
{/if}