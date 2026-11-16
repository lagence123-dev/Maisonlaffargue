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
	{if $input.type == 'cms_pages'}

				<div class="col-lg-8">
					<div class="panel col-lg-7">


                        <table width="50%" cellspacing="0" cellpadding="0" class="table">
                            <tr>
                                <th>{l s='Shop' mod='blockblog'}</th>
                            </tr>
                            {assign var=i value=0}
                            {foreach $input.values as $_shop}
                            <tr>
                                <td>

                                    <img src="../modules/blockblog/views/img/lv2_{if count($input.values)-1 == $i}f{else}b{/if}.png" alt="{$_shop['name']|escape:'htmlall':'UTF-8'}" style="vertical-align:middle;">
                                    <label class="child">
                                        <input type="checkbox" class="input_shop" {if $_shop['id_shop']|in_array:$input.selected_data}checked="checked"{/if} value="{$_shop['id_shop']|escape:'htmlall':'UTF-8'}" name="cat_shop_association[]">
                                        {$_shop['name']|escape:'htmlall':'UTF-8'}
                                    </label>
                                </td>
                            </tr>
                                {assign var=i value=$i++}
                            {/foreach}
                        </table>



					</div>
					{if isset($input.desc) && !empty($input.desc)}
						<p class="help-block">
							{$input.desc|escape:'htmlall':'UTF-8'}
						</p>
					{/if}
				</div>
    {elseif $input.type == 'item_date'}

        <div class="row">
            <div class="input-group col-lg-4">
                <input id="{if isset($input.id)}{$input.id|escape:'htmlall':'UTF-8'}{else}{$input.name|escape:'htmlall':'UTF-8'}{/if}"
                       type="text" data-hex="true"
                       {if isset($input.class)}class="{$input.class}"
                       {else}class="item_datepicker"{/if} name="time_add" value="{$input.time_add|escape:'html':'UTF-8'}" />
                <span class="input-group-addon"><i class="icon-calendar-empty"></i></span>
            </div>
        </div>

    {literal}

        <script type="text/javascript">
            $('document').ready( function() {

                var dateObj = new Date();
                var hours = dateObj.getHours();
                var mins = dateObj.getMinutes();
                var secs = dateObj.getSeconds();
                if (hours < 10) { hours = "0" + hours; }
                if (mins < 10) { mins = "0" + mins; }
                if (secs < 10) { secs = "0" + secs; }
                var time = " "+hours+":"+mins+":"+secs;

                if ($(".item_datepicker").length > 0)
                    $(".item_datepicker").datepicker({prevText: '',nextText: '',dateFormat: 'yy-mm-dd'+time});

            });
        </script>
    {/literal}


    {elseif $input.type == 'cat_image_custom'}

        <div class="col-lg-8">

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
                </p>
            {/if}
            {if isset($input.is_demo) && !empty($input.is_demo)}
                {$input.is_demo|escape:'quotes':'UTF-8'}
            {/if}

            {if isset($input.logo_img) && $input.logo_img != ''}
                <div class="form-group" id="post_images_list">
                    <input type="radio" name="post_images" checked="" style="display: none">
                    <div id="{$input.name|escape:'htmlall':'UTF-8'}-images-thumbnails" class="col-lg-12">
                        <img src="{$input.logo_img_path|escape:'htmlall':'UTF-8'}" class="img-thumbnail" style="width: 200px"/>

                    </div>

                    <a class="delete_product_image btn btn-default" href="javascript:void(0)"
                       onclick = "delete_img_cat({$input.id_post|escape:'htmlall':'UTF-8'});"
                       style="margin-top: 10px">
                        <i class="icon-trash"></i> {l s='Delete this image' mod='blockblog'}
                    </a>

                </div>
            {/if}


        </div>
    {elseif $input.type == 'group_association'}

        <div class="col-lg-8">
            <div class="panel col-lg-7">

                <table width="50%" cellspacing="0" cellpadding="0" class="table">
                    <thead>
                    <tr>
                        <th>
                            <input name="checkme" class="noborder" onclick="checkDelBoxes(this.form, '{$input.name|escape:'htmlall':'UTF-8'}[]', this.checked)" type="checkbox"/>
                        </th>
                        <th>{l s='ID' mod='blockblog'}</th>
                        <th>{l s='Group' mod='blockblog'}</th>

                    </tr>
                    </thead>
                    <tbody>
                    {assign var=i value=0}
                    {foreach $input.values as $_item}
                        <tr>
                            <td>
                                <input type="checkbox" class="input_shop" {if $_item['id_group']|in_array:$input.selected_data}checked="checked"{/if}
                                       value="{$_item['id_group']|escape:'htmlall':'UTF-8'}" name="{$input.name|escape:'htmlall':'UTF-8'}[]">
                            </td>
                            <td>


                                {$_item['id_group']|escape:'htmlall':'UTF-8'}
                            </td>
                            <td>

                                {$_item['name']|escape:'htmlall':'UTF-8'}
                            </td>

                        </tr>
                        {assign var=i value=$i++}
                    {/foreach}
                    </tbody>
                </table>






            </div>
            {if isset($input.desc) && !empty($input.desc)}
                <div style="clear:both"></div>
                <p class="help-block">
                    {$input.desc|escape:'htmlall':'UTF-8'}
                </p>
            {/if}
        </div>


    {elseif $input.type == 'related_posts'}

        <div class="col-lg-8">
            <div class="panel col-lg-9" style="height:200px; overflow-x:hidden; overflow-y:scroll;">


                <table width="50%" cellspacing="0" cellpadding="0" class="table">
                    <thead>
                    <tr>
                        <th><input name="checkme" class="noborder" onclick="checkDelBoxes(this.form, '{$input.name_field_custom|escape:'htmlall':'UTF-8'}[]', this.checked)" type="checkbox"/></th>
                        <th>{l s='ID' mod='blockblog'}</th>
                        <th>{l s='Title' mod='blockblog'}</th>
                        <th>{l s='Language' mod='blockblog'}</th>
                        <th>{l s='Status' mod='blockblog'}</th>
                    </tr>
                    </thead>
                    <tbody>
                    {assign var=i value=0}
                    {foreach $input.values as $_item}
                        <tr>
                            <td>
                                {*{$input.selected_data|@var_dump}*}
                                <input type="checkbox" class="input_shop" {if $_item['id']|in_array:$input.selected_data}checked="checked"{/if}
                                       value="{$_item['id']|escape:'htmlall':'UTF-8'}" name="{$input.name_field_custom|escape:'htmlall':'UTF-8'}[]">
                            </td>
                            <td>


                                {$_item['id']|escape:'htmlall':'UTF-8'}
                            </td>
                            <td>



                                {if strlen($_item['img'])>0}
                                    <img src="{$input.logo_img_path|escape:'htmlall':'UTF-8'}{$_item['img']|escape:'htmlall':'UTF-8'}" class="img-thumbnail" style="width: 50px;margin-right:10px"/>
                                {/if}

                                {*<a href="
                                        {if $input.is_rewrite == 0}
                                            {$input.item_url|escape:'htmlall':'UTF-8'}{$_item['id']|escape:'htmlall':'UTF-8'}
                                        {else}
                                            {$input.item_url|escape:'htmlall':'UTF-8'}{$_item['seo_url']|escape:'htmlall':'UTF-8'}
                                        {/if}

                                    " target="_blank" title="{$_item['title']|escape:'htmlall':'UTF-8'}">*}

                                    {$_item['title']|escape:'htmlall':'UTF-8'}

                                {*</a>*}
                            </td>
                            <td>

                                {$_item['iso_lang']|escape:'htmlall':'UTF-8'}
                            </td>
                            <td>
                                <img src="../img/admin/../../modules/blockblog/views/img/{if $_item['status'] == 1}ok.gif{else}no_ok.gif{/if}"  />
                            </td>
                        </tr>
                        {assign var=i value=$i++}
                    {/foreach}
                    </tbody>
                </table>


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
