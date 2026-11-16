{*
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
*}

{extends file="helpers/form/form.tpl"}
{block name="field"}
    {if $input.type == 'files_lang'}
        <div class="row bonmp-files-upload">
            {foreach from=$languages item=language}
                {if $languages|count > 1}
                    <div class="translatable-field lang-{$language.id_lang|escape:'htmlall':'UTF-8'}"
                        {if $language.id_lang != $defaultFormLanguage}style="display:none" {/if}>
                    {/if}
                    <div class="col-lg-6">
                        <div class="dummyfile input-group">
                            <input id="{$input.name|escape:'htmlall':'UTF-8'}_{$language.id_lang|escape:'htmlall':'UTF-8'}"
                                type="file"
                                name="{$input.name|escape:'htmlall':'UTF-8'}_{$language.id_lang|escape:'htmlall':'UTF-8'}"
                                class="hide-file-upload" />
                            <span class="input-group-addon">
                                <i class="icon-file"></i>
                            </span>
                            <input id="{$input.name|escape:'htmlall':'UTF-8'}_{$language.id_lang|escape:'htmlall':'UTF-8'}-name"
                                type="text" class="disabled" name="filename" readonly />
                            <span class="input-group-btn">
                                <button
                                    id="{$input.name|escape:'htmlall':'UTF-8'}_{$language.id_lang|escape:'htmlall':'UTF-8'}-selectbutton"
                                    type="button" name="submitAddAttachments" class="btn btn-default">
                                    <i class="icon-folder-open"></i>
                                    {l s='Choose a file' mod='bonmaintenance'}
                                </button>
                            </span>
                        </div>
                        {if isset($fields[0]['form']['images'][$language.id_lang|escape:'htmlall':'UTF-8']) && $fields[0]['form']['images'][$language.id_lang|escape:'htmlall':'UTF-8']}
                            <img src="{$image_baseurl|escape:'htmlall':'UTF-8'}{$fields[0]['form']['images'][$language.id_lang|escape:'htmlall':'UTF-8']}" class="img-thumbnail" />
                        {/if}
                    </div>
                    {if $languages|count > 1}
                        <div class="col-lg-2">
                            <button type="button" class="btn btn-default dropdown-toggle" tabindex="-1" data-toggle="dropdown">
                                {$language.iso_code|escape:'htmlall':'UTF-8'}
                                <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu">
                                {foreach from=$languages item=lang}
                                    <li>
                                        <a href="javascript:hideOtherLanguage({$lang.id_lang|escape:'htmlall':'UTF-8'});"
                                            tabindex="-1">{$lang.name|escape:'htmlall':'UTF-8'}</a>
                                    </li>
                                {/foreach}
                            </ul>
                        </div>
                    {/if}
                    {if $languages|count > 1}
                    </div>
                {/if}
                <script>
                    $(document).ready(function() {
                        $('#{$input.name|escape:"htmlall":"UTF-8"}_{$language.id_lang|escape:"htmlall":"UTF-8"}-selectbutton').click(function(e){
                        $('#{$input.name|escape:"htmlall":"UTF-8"}_{$language.id_lang|escape:"htmlall":"UTF-8"}').trigger('click');
                    });
                    $('#{$input.name|escape:"htmlall":"UTF-8"}_{$language.id_lang|escape:"htmlall":"UTF-8"}').change(function(e){
                    var val = $(this).val();
                    var file = val.split(/[\\/]/);
                    $('#{$input.name|escape:"htmlall":"UTF-8"}_{$language.id_lang|escape:"htmlall":"UTF-8"}-name').val(file[file.length-1]);
                    });
                    });
                </script>
            {/foreach}
        </div>
    {/if}
    {$smarty.block.parent}
{/block}