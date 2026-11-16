{**
 *   Copyright since 2009 ohmyweb!
 *
 *   @author    ohmyweb <contact@ohmyweb.fr>
 *   @copyright since 2009 ohmyweb!
 *   @license   Proprietary - no redistribution without authorization
 *}

<div class="config-container">
    <input type="hidden" name="config" value="{$jsonConfig|escape:'html'}" />
    <div id="cef_config" class="col-12">
        <div class="banner_up">
            <div class="col-xs-3 col-xs-offset-1"><span>{l s='Identifier' d='Modules.Dwfproductextrafields.Admin'}</span></div>
            <div class="col-xs-4"><span>{l s='Name' d='Admin.Global'}</span></div>
            <div class="col-xs-3"><span> {l s='Type' d='Modules.Dwfproductextrafields.Admin'}</span></div>
        </div>
        <ul class="admin_selector clearfix" id="repeater_list">
            {assign var=config value=$jsonConfig|json_decode:true}
            {if !empty($config.elements)}
                {foreach from=$config.elements item=val name=configRow}
                    <li id="repeater_{$smarty.foreach.configRow.index}" class="selector_item">
                        <div class="row">
                            <div class="drp-drag col-xs-1"><i class="icon-bars" aria-hidden="true"></i></div>
                            <div class="drp-key col-xs-3"><input value="{$val.key}" placeholder="{l s='Identifier' d='Modules.Dwfproductextrafields.Admin'}" type="text"></div>
                            <div class="drp-name col-xs-4">
                                {foreach from=$languages item=lang}
                                    {if $languages|count > 1}
                                        <div class="translatable-field lang-{$lang.id_lang}"{if $lang.id_lang != $default_lang} style="display:none;"{/if}>
                                        <div class="col-xs-9">
                                    {/if}

                                    {assign var=key value=(array_search($lang.id_lang, array_column($val.name, 'id_lang')))}
                                    <input value="{if isset($val.name[$key].value)}{$val.name[$key].value}{/if}" placeholder="{l s='Name' d='Admin.Global'}" type="text">
                                    {if $languages|count > 1}
                                        </div>
                                        <div class="col-xs-2">
                                            <button type="button" class="btn btn-default dropdown-toggle" tabindex="-1" data-toggle="dropdown">{$lang.iso_code}<i class="icon-caret-down"></i></button>
                                            <ul class="dropdown-menu">
                                                {foreach from=$languages item=lng}
                                                    <li><a href="javascript:hideOtherLanguage({$lng.id_lang});" tabindex="-1">{$lng.name}</a></li>
                                                {/foreach}
                                            </ul>
                                        </div>
                                        </div>
                                    {/if}
                                {/foreach}
                            </div>
                            <div class="drp-type col-xs-3">
                                <select>
                                    <option value="text"{if $val.type == 'text'} selected="selected"{/if}>{l s='Text' d='Modules.Dwfproductextrafields.Admin'}</option>
                                    <option value="textarea_mce"{if $val.type == 'textarea_mce'} selected="selected"{/if}>{l s='Textarea with tinymce editor' d='Modules.Dwfproductextrafields.Admin'}</option>
                                    <option value="textarea"{if $val.type == 'textarea'} selected="selected"{/if}>{l s='Textarea' d='Modules.Dwfproductextrafields.Admin'}</option>
                                    <option value="checkbox"{if $val.type == 'checkbox'} selected="selected"{/if}>{l s='Checkbox' d='Modules.Dwfproductextrafields.Admin'}</option>
                                    <option value="image"{if $val.type == 'image'} selected="selected"{/if}>{l s='Image' d='Modules.Dwfproductextrafields.Admin'}</option>
                                    {*<option value="integer">{l s='Integer' d='Modules.Dwfproductextrafields.Admin'}</option>*}
                                    {*<option value="decimal">{l s='Decimal' d='Modules.Dwfproductextrafields.Admin'}</option>*}
                                    {*<option value="price">{l s='Price' d='Modules.Dwfproductextrafields.Admin'}</option>*}
                                    {*<option value="date">{l s='Date' d='Modules.Dwfproductextrafields.Admin'}</option>*}
                                    {*<option value="datetime">{l s='Datetime' d='Modules.Dwfproductextrafields.Admin'}</option>*}
                                </select>
                            </div>
                            <div class="drp-rmv col-xs-1"><button type="button" class="btn btn-small" title="{l s='Remove' d='Admin.Actions'}"><i class="icon-trash" aria-hidden="true"></i></button></div>
                        </div>
                    </li>
                {/foreach}
            {else}
                <li id="repeater_0" class="selector_item">
                    <div class="row">
                        <div class="drp-drag col-xs-1"><i class="icon-bars" aria-hidden="true"></i></div>
                        <div class="drp-key col-xs-3"><input value="" placeholder="{l s='Identifier' d='Modules.Dwfproductextrafields.Admin'}" type="text"></div>
                        <div class="drp-name col-xs-4">
                            {foreach from=$languages item=lang}
                                {if $languages|count > 1}
                                    <div class="translatable-field lang-{$lang.id_lang}"{if $lang.id_lang != $default_lang} style="display:none;"{/if}>
                                    <div class="col-xs-9">
                                {/if}
                                <input value="" placeholder="{l s='Name' d='Admin.Global'}" type="text">
                                {if $languages|count > 1}
                                    </div>
                                    <div class="col-xs-2">
                                        <button type="button" class="btn btn-default dropdown-toggle" tabindex="-1" data-toggle="dropdown">{$lang.iso_code}<i class="icon-caret-down"></i></button>
                                        <ul class="dropdown-menu">
                                            {foreach from=$languages item=lng}
                                                <li><a href="javascript:hideOtherLanguage({$lng.id_lang});" tabindex="-1">{$lng.name}</a></li>
                                            {/foreach}
                                        </ul>
                                    </div>
                                    </div>
                                {/if}
                            {/foreach}
                        </div>
                        <div class="drp-type col-xs-3">
                            <select>
                                <option value="text">{l s='Text' d='Modules.Dwfproductextrafields.Admin'}</option>
                                <option value="textarea_mce">{l s='Textarea with tinymce editor' d='Modules.Dwfproductextrafields.Admin'}</option>
                                <option value="textarea">{l s='Textarea' d='Modules.Dwfproductextrafields.Admin'}</option>
                                <option value="checkbox">{l s='Checkbox' d='Modules.Dwfproductextrafields.Admin'}</option>
                                <option value="image">{l s='Image' d='Modules.Dwfproductextrafields.Admin'}</option>
                            </select>
                        </div>
                        <div class="drp-rmv col-xs-1"><button type="button" class="btn btn-small" title="{l s='Remove' d='Admin.Actions'}"><i class="icon-trash" aria-hidden="true"></i></button></div>
                    </div>
                </li>
            {/if}
        </ul>
        <div class="banner_down">
            <div class="col-xs-6"><label for="collapsed_default"><span>{l s='Collapsed by default:' d='Modules.Dwfproductextrafields.Admin'}</span> <input id="collapsed_default" type="checkbox"{if isset($config.collapsed_default) && $config.collapsed_default} checked="checked"{/if} /></label></div>
            <div class="col-xs-6 text-center"><button type="button" id="repeater_ad-optn" class="btn btn-sm">{l s='Add element' d='Modules.Dwfproductextrafields.Admin'} <i class="icon-plus" aria-hidden="true"></i></button></div>
        </div>

        <script type="text/javascript">
            {literal}
            $(document).ready(function() {
                template_new_row = "<div class=\"row\"><div class=\"drp-drag col-xs-1\"><i class=\"icon-bars\" aria-hidden=\"true\"></i></div>";
                template_new_row += "<div class=\"drp-key col-xs-3\"><input value=\"\" placeholder=\"{/literal}{l s='Identifier' d='Modules.Dwfproductextrafields.Admin'}{literal}\" type=\"text\"></div>";
                template_new_row += "<div class=\"drp-name col-xs-4\">";
                languages.map(function (lang) {
                    if (languages.length > 1) {
                        template_new_row += "<div class=\"translatable-field lang-" + lang.id_lang + "\"" + (lang.id_lang != id_language ? " style=\"display:none\"" : "") + "><div class=\"col-xs-9\">";
                    }
                    template_new_row += "<input value=\"\" placeholder=\"{/literal}{l s='Name' d='Admin.Global'}{literal}\" type=\"text\">";
                    if (languages.length > 1) {
                        template_new_row += "</div><div class=\"col-xs-2\"><button type=\"button\" class=\"btn btn-default dropdown-toggle\" tabindex=\"-1\" data-toggle=\"dropdown\">" + lang.iso_code + "<i class=\"icon-caret-down\"></i></button><ul class=\"dropdown-menu\">";
                        languages.map(function (lng) {
                            template_new_row += "<li><a href=\"javascript:hideOtherLanguage(" + lng.id_lang + ");\" tabindex=\"-1\">" + lng.name + "</a></li>";
                        });
                        template_new_row += "</ul></div></div>";
                    }
                });
                template_new_row += "</div><div class=\"drp-type col-xs-3\">";
                template_new_row += "<select>";
                template_new_row += "<option value=\"text\">{/literal}{l s='Text' d='Modules.Dwfproductextrafields.Admin'}{literal}</option>";
                template_new_row += "<option value=\"textarea_mce\">{/literal}{l s='Textarea with tinymce editor' d='Modules.Dwfproductextrafields.Admin'}{literal}</option>";
                template_new_row += "<option value=\"textarea\">{/literal}{l s='Textarea' d='Modules.Dwfproductextrafields.Admin'}{literal}</option>";
                template_new_row += "<option value=\"checkbox\">{/literal}{l s='Checkbox' d='Modules.Dwfproductextrafields.Admin'}{literal}</option>";
                template_new_row += "<option value=\"image\">{/literal}{l s='Image' d='Modules.Dwfproductextrafields.Admin'}{literal}</option>";
                template_new_row += "</select>";
                template_new_row += "</div><div class=\"drp-rmv col-xs-1\"><button type=\"button\" class=\"btn btn-small\" title=\"{/literal}{l s='Remove' d='Admin.Actions'}{literal}\"><i class=\"icon-trash\" aria-hidden=\"true\"></i></button></div></div>";
            });
            {/literal}
        </script>
    </div>
</div>
