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
{block name="field"}
	{if $input.type == 'checkbox_custom'}
        <div class="col-lg-8 {$input.name|escape:'htmlall':'UTF-8'}">

            <input type="checkbox" name="{$input.name|escape:'htmlall':'UTF-8'}" id="{$input.name|escape:'htmlall':'UTF-8'}"
                   value="1" {if $input.values.value == 1} checked="checked"{/if} />



            {if isset($input.desc) && !empty($input.desc)}
                <p class="help-block">
                    {$input.desc|escape:'htmlall':'UTF-8'}
                </p>
            {/if}
        </div>

    {elseif $input.type == 'avatar_custom'}

        <div class="col-lg-8 margin-form">

            <div class="form-group">
                <div class="col-lg-6" >
                    <input id="{$input.name|escape:'htmlall':'UTF-8'}" type="file" name="{$input.name|escape:'htmlall':'UTF-8'}" class="hide" />
                    <div class="dummyfile input-group">
                        <span class="input-group-addon"><i class="icon-file"></i></span>
                        <input id="{$input.name|escape:'htmlall':'UTF-8'}-name" type="text" class="disabled" name="filename" readonly />
							<span class="input-group-btn">
								<button id="{$input.name|escape:'htmlall':'UTF-8'}-selectbutton" type="button" name="submitAddAttachments" class="btn btn-default">
                                    <i class="icon-folder-open"></i> {l s='Choose a file' mod='blockblog'}
                                </button>
							</span>
                    </div>

                    {literal}
                    <script type="text/javascript">
                        $(document).ready(function(){
                            $('#{/literal}{$input.name|escape:'htmlall':'UTF-8'}{literal}-selectbutton').click(function(e){
                                $('#{/literal}{$input.name|escape:'htmlall':'UTF-8'}{literal}').trigger('click');
                            });
                            $('#{/literal}{$input.name|escape:'htmlall':'UTF-8'}{literal}').change(function(e){
                                var val = $(this).val();
                                var file = val.split(/[\/]/);
                                $('#{/literal}{$input.name|escape:'htmlall':'UTF-8'}{literal}-name').val(file[file.length-1]);
                            });
                        });

                        var ajax_link_blockblog = '{/literal}{$input.ajax_url nofilter}{literal}';
                    </script>
                    {/literal}


                </div>

            </div>
            {if isset($input.desc) && !empty($input.desc)}
                <p class="help-block">
                    {$input.desc|escape:'htmlall':'UTF-8'}
                    <br/>
                    <span style="color:black:font-size:13px">{l s='Max file size in php.ini' mod='blockblog'}: <b style="color:green">{$input.max_upload_info|escape:'htmlall':'UTF-8'}</b></span>
                    <br/>
                    <span style="color:black:font-size:13px">{l s='Max POST size (post_max_size) in php.ini' mod='blockblog'}: <b style="color:green">{$input.post_max_size|escape:'htmlall':'UTF-8'}</b></span>
                </p>
            {/if}
            {if isset($input.is_demo) && !empty($input.is_demo)}
                {$input.is_demo|escape:'quotes':'UTF-8'}
            {/if}

            <span class="avatar-form">
                <input type="hidden" name="id_customer" value="{$input.id_customer|escape:'htmlall':'UTF-8'}" />

                {if $input.is_exist_ava>0}
                    <input type="radio" name="post_images_ava" checked="" style="display: none">
                    <img src="{$input.value|escape:'htmlall':'UTF-8'}" />
                    <br/>
                    <a class="delete_product_image btn btn-default" href="javascript:void(0)"
                       onclick = "delete_avatar({$input.post_id|escape:'htmlall':'UTF-8'},{$input.id_customer|escape:'htmlall':'UTF-8'},1);"
                       style="margin-top: 10px">
                        <i class="icon-trash"></i> {l s='Delete avatar and use standart empty avatar' mod='blockblog'}
                    </a>

                {else}
                <img src = "../modules/blockblog/views/img/avatar_m.gif" />
                {/if}
            </span>

        </div>


    {elseif $input.type == 'language_item'}


        <div class="col-lg-8 margin-form">


            <div class="form-group margin-item-form-top-left">
                <span class="badge">
                {$input.values|escape:'htmlall':'UTF-8'}
                    </span>
            </div>





            {if isset($input.desc) && !empty($input.desc)}
                <p class="help-block">
                    {$input.desc|escape:'htmlall':'UTF-8'}
                </p>
            {/if}
        </div>

    {elseif $input.type == 'count_posts_item'}


        <div class="col-lg-8 margin-form">


            <div class="form-group margin-item-form-top-left">
                <span class="label-tooltip" data-original-title="{l s='Total' mod='blockblog'}" data-toggle="tooltip">
                    <span class="badge">
                        {$input.values|escape:'htmlall':'UTF-8'}
                     </span>
                </span>

                <span style="font-size:14px;font-weight:bold">
                    (
                    <span class="label-tooltip" data-original-title="{l s='Enabled' mod='blockblog'}" data-toggle="tooltip">

                    <span style="color:green">{$input.active|escape:'htmlall':'UTF-8'}</span>
                        </span>
                    /
                    <span class="label-tooltip" data-original-title="{l s='Disabled' mod='blockblog'}" data-toggle="tooltip">
                 <span style="color:red">{$input.noactive|escape:'htmlall':'UTF-8'}</span>
                        </span>
                    )
                <span>

            </div>





            {if isset($input.desc) && !empty($input.desc)}
                <p class="help-block">
                    {$input.desc|escape:'htmlall':'UTF-8'}
                </p>
            {/if}
        </div>

    {elseif $input.type == 'customer_url'}

        <div class="col-lg-8 margin-form">


            <div class="form-group margin-item-form-top-left">
                <span class="badge">
                    <a href="
                    {if $input.is_rewrite == 0}
                            {$input.author_url|escape:'htmlall':'UTF-8'}{$input.id_customer|escape:'htmlall':'UTF-8'}-{$input.values|escape:'htmlall':'UTF-8'}
                        {else}

                            {$input.base_dir_ssl|escape:'htmlall':'UTF-8'}{if isset($input.data_shop_uris[$input.id_shop])}{$input.data_shop_uris[$input.id_shop]|escape:'htmlall':'UTF-8'}{/if}{if $input.count_languages > 1}{$input.iso_code|escape:'htmlall':'UTF-8'}{/if}{$input.alias_url_blog|escape:'htmlall':'UTF-8'}/{$input.alias_url|escape:'htmlall':'UTF-8'}/{$input.id_customer|escape:'htmlall':'UTF-8'}-{$input.values|escape:'htmlall':'UTF-8'}
                        {/if}
                    {*{$input.url|escape:'htmlall':'UTF-8'}*}
                    "
                       target="_blank" title="{$input.url|escape:'htmlall':'UTF-8'}">{$input.values|escape:'htmlall':'UTF-8'}</a>

                    </span>
            </div>



            {if isset($input.desc) && !empty($input.desc)}
                <p class="help-block">
                    {$input.desc|escape:'htmlall':'UTF-8'}
                </p>
            {/if}
        </div>

	{else}
		{$smarty.block.parent}
	{/if}
{/block}
