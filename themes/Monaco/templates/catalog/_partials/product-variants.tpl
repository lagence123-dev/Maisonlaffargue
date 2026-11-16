{**
* 2007-2020 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License 3.0 (AFL-3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* https://opensource.org/licenses/AFL-3.0
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
* @author PrestaShop SA <contact@prestashop.com>
    * @copyright 2007-2020 PrestaShop SA
    * @license https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
    * International Registered Trademark & Property of PrestaShop SA
    *}
<div class="product-variants">
    {foreach from=$groups key=id_attribute_group item=group}
        {if !empty($group.attributes)}
            <div class="clearfix product-variants-item">
                {if $group.group_type == 'select'}
                    <span class="control-label">{$group.name} :</span>
                    <select class="form-control form-control-select" id="group_{$id_attribute_group}" data-product-attribute="{$id_attribute_group}" name="group[{$id_attribute_group}]">
                        {foreach from=$group.attributes key=id_attribute item=group_attribute}
                            <option value="{$id_attribute}" title="{$group_attribute.name}" {if $group_attribute.selected} selected="selected" {/if}>{$group_attribute.name}</option>
                        {/foreach}
                    </select>
					<p></br>Sur notre e-boutique, les tailles sont proposées de 5 cm en 5 cm, mais nous pouvons aussi réaliser les ceintures au centimètre près : si vous souhaitez une taille précise, vous pouvez l'indiquer en choisissant Autre dans le menu déroulant.</p>
                {elseif $group.group_type == 'color'}
                    {foreach from=$group.attributes key=id_attribute item=group_attribute}
                        {if $group_attribute.selected}{assign var="color_selected" value=$group_attribute.name}{/if}
                    {/foreach}
                    <span class="control-label">{$group.name} : {$color_selected}</span>
                    <ul id="group_{$id_attribute_group}" class="color-variants">
                        {foreach from=$group.attributes key=id_attribute item=group_attribute}
                            <li class="float-xs-left input-container" /*data-color="{$group_attribute.name}*/" title="{$group_attribute.name}">
                                <label class="{if $group_attribute.selected}check-color{/if}">
                                    <input class="input-color" type="radio" data-product-attribute="{$id_attribute_group}" name="group[{$id_attribute_group}]" value="{$id_attribute}" {if $group_attribute.selected} checked="checked" {/if}>
                                    <span {if $group_attribute.html_color_code}class="color{if $group_attribute.selected} check-color{/if}" style="background-color: {$group_attribute.html_color_code}" {/if} {if $group_attribute.texture}class="color texture {if $group_attribute.selected} check-color{/if}" style="background-image: url({$group_attribute.texture})" {/if}><span class="sr-only">{$group_attribute.name}</span></span>
                                </label>
                            </li>
                        {/foreach}
						{hook h='displayProductVari' product=$product type="after_price"}
                    </ul>
                {elseif $group.group_type == 'radio'}
                    <span class="control-label">{$group.name} :</span>
                    <ul id="group_{$id_attribute_group}">
                        {foreach from=$group.attributes key=id_attribute item=group_attribute}
                            <li class="input-container float-xs-left">
                                <label>
                                    <input class="input-radio" type="radio" data-product-attribute="{$id_attribute_group}" name="group[{$id_attribute_group}]" value="{$id_attribute}" {if $group_attribute.selected} checked="checked" {/if}>
                                    <span class="radio-label{if $group_attribute.selected} check{/if}">{$group_attribute.name}</span>
                                </label>
                            </li>
                        {/foreach}
                    </ul>
                {/if}
            </div>
        {/if}
    {/foreach}
</div>