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
        <div class="col-lg-8 {$input.name|escape:'htmlall':'UTF-8'}" style="padding: 7px">

            <input type="checkbox" name="{$input.name|escape:'htmlall':'UTF-8'}" id="{$input.name|escape:'htmlall':'UTF-8'}"
                   value="1" {if $input.values.value == 1} checked="checked"{/if} />



            {if isset($input.desc) && !empty($input.desc)}
                <p class="help-block">
                    {$input.desc|escape:'htmlall':'UTF-8'}
                </p>
            {/if}
        </div>

    {elseif $input.type == 'checkbox_custom_loyalty'}
        <div class="col-lg-8 {$input.name|escape:'htmlall':'UTF-8'}">

            {foreach $input.values.query as $value}
                {assign var=id_checkbox value=$value[$input.values.id]}
                <div class="checkbox{if isset($input.expand) && strtolower($input.expand.default) == 'show'} hidden{/if}">

                    {strip}
                        <label for="{$id_checkbox|escape:'htmlall':'UTF-8'}">
                            <input type="checkbox" name="{$id_checkbox|escape:'htmlall':'UTF-8'}" id="{$id_checkbox|escape:'htmlall':'UTF-8'}" class="{if isset($input.class)}{$input.class|escape:'htmlall':'UTF-8'}{/if}"{if isset($value.val)} value="{$value.val|escape:'htmlall':'UTF-8'}"{/if}{if isset($fields_value[$id_checkbox]) && $fields_value[$id_checkbox]} checked="checked"{/if} />
                            {$value[$input.values.name]|escape:'htmlall':'UTF-8'}
                        </label>
                    {/strip}
                </div>
            {/foreach}

            {if isset($input.desc) && !empty($input.desc)}
                <p class="help-block">
                    {$input.desc|escape:'htmlall':'UTF-8'}
                </p>
            {/if}
        </div>

    {elseif $input.type == 'block_radio_buttons_blog_comments_custom'}


        <div class="col-lg-6 {$input.name|escape:'htmlall':'UTF-8'}">
            <div class="panel">


                <table class="table blockblog-table-td">

                    <tbody>


                    <tr class="alt_row">
                        <td>
                            <input type="radio" value="2" id="whocanaddc" name="whocanaddc"
                                    {if $input.values.value == 2} checked="checked" {/if}/>
                            {l s='Only registered users' mod='blockblog'}
                        </td>
                        <td>
                            <input type="radio" value="1" id="whocanaddc" name="whocanaddc"
                                    {if $input.values.value == 1} checked="checked" {/if}
                                    />
                            {l s='All users' mod='blockblog'}
                        </td>
                    </tr>



                    </tbody>
                </table>
            </div>

            {if isset($input.desc) && !empty($input.desc)}
                <p class="help-block">
                    {$input.desc|escape:'htmlall':'UTF-8'}
                </p>
            {/if}
        </div>

    {elseif $input.type == 'image_custom_px'}
        <div class="input-group col-lg-1">
            <input type="text" name="{$input.name|escape:'htmlall':'UTF-8'}" id="{$input.name|escape:'htmlall':'UTF-8'}"
                   value="{$input.value|escape:'htmlall':'UTF-8'}" />
            <span class="input-group-addon">&nbsp;px</span>




        </div>
        {if isset($input.desc) && !empty($input.desc)}
            <div class="col-lg-3">&nbsp;</div>
            <p class="help-block">
                {$input.desc nofilter}
            </p>
        {/if}


    {elseif $input.type == 'custom_ms'}
        <div class="input-group col-lg-1">
            <input type="text" name="{$input.name|escape:'htmlall':'UTF-8'}" id="{$input.name|escape:'htmlall':'UTF-8'}"
                   value="{$input.value|escape:'htmlall':'UTF-8'}" />
            <span class="input-group-addon">&nbsp;ms</span>



        </div>

    {elseif $input.type == 'text_truncate'}
        <div class="input-group col-lg-2">
            <input type="text" name="{$input.name|escape:'htmlall':'UTF-8'}" id="{$input.name|escape:'htmlall':'UTF-8'}"
                   value="{$input.value|escape:'htmlall':'UTF-8'}" />
            <span class="input-group-addon">&nbsp;{l s='chars' mod='blockblog'}</span>


        </div>

    {elseif $input.type == 'checkbox_custom_blocks'}
        <div class="col-lg-8 {$input.name|escape:'htmlall':'UTF-8'}">

            {foreach $input.values.query as $value}
                {assign var=id_checkbox value=$value[$input.values.id]}
                <div class="checkbox{if isset($input.expand) && strtolower($input.expand.default) == 'show'} hidden{/if}">

                    {strip}
                        <label for="{$id_checkbox|escape:'htmlall':'UTF-8'}">
                            <input type="checkbox" name="{$id_checkbox|escape:'htmlall':'UTF-8'}" id="{$id_checkbox|escape:'htmlall':'UTF-8'}"
                                   class="{if isset($input.class)}{$input.class}{/if}"{if isset($value.val)}
                            value="{$value.val|escape:'htmlall':'UTF-8'}"{/if}{if isset($fields_value[$id_checkbox]) && $fields_value[$id_checkbox]} checked="checked"{/if} />
                            {$value[$input.values.name]|escape:'htmlall':'UTF-8'}
                        </label>
                    {/strip}
                </div>
            {/foreach}

            {if isset($input.desc) && !empty($input.desc)}
                <p class="help-block">
                    {$input.desc|escape:'htmlall':'UTF-8'}
                </p>
            {/if}
        </div>

    {elseif $input.type == 'checkbox_custom_blocks_custom'}
        <div class="col-lg-8 {$input.name|escape:'htmlall':'UTF-8'}">

            {foreach $input.values.query as $value}
                {assign var=id_checkbox value=$value[$input.values.id]}
                <div class="checkbox{if isset($input.expand) && strtolower($input.expand.default) == 'show'} hidden{/if}">

                    {strip}
                        <label for="{$id_checkbox|escape:'htmlall':'UTF-8'}">
                            <input type="checkbox" name="{$id_checkbox|escape:'htmlall':'UTF-8'}" id="{$id_checkbox|escape:'htmlall':'UTF-8'}"
                                   class="{if isset($input.class)}{$input.class}{/if}"{if isset($value.val)}
                            value="{$value.val|escape:'htmlall':'UTF-8'}"{/if}{if isset($fields_value[$id_checkbox]) && $fields_value[$id_checkbox]} checked="checked"{/if} />
                            {$value[$input.values.name]|escape:'htmlall':'UTF-8'}
                        </label>
                    {/strip}


                    -

                    {strip}
                        <label for="s{$id_checkbox|escape:'htmlall':'UTF-8'}">
                            <input type="checkbox" name="s{$id_checkbox|escape:'htmlall':'UTF-8'}" id="s{$id_checkbox|escape:'htmlall':'UTF-8'}"
                                   class="{if isset($input.class)}{$input.class|escape:'htmlall':'UTF-8'}{/if}"
                                   value="1"
                                    {if isset($value.site) && $value.site} checked="checked"{/if}
                                    />
                            {l s='display on the site view' mod='blockblog'}
                        </label>
                    {/strip}

                    -

                    {strip}
                        <label for="m{$id_checkbox|escape:'htmlall':'UTF-8'}">
                            <input type="checkbox" name="m{$id_checkbox|escape:'htmlall':'UTF-8'}" id="m{$id_checkbox|escape:'htmlall':'UTF-8'}"
                                   class="{if isset($input.class)}{$input.class|escape:'htmlall':'UTF-8'}{/if}"
                                   value="1"
                                    {if isset($value.mobile) && $value.mobile} checked="checked"{/if}
                                    />
                            {l s='display on the mobile view' mod='blockblog'}
                        </label>
                    {/strip}

                </div>
            {/foreach}

            {if isset($input.desc) && !empty($input.desc)}
                <p class="help-block">
                    {$input.desc|escape:'htmlall':'UTF-8'}
                </p>
            {/if}
        </div>

    {elseif $input.type == 'blog_display_effect'}
        <div class="input-group col-lg-2">

            {$input.value nofilter}

         </div>

    {elseif $input.type == 'order_items'}
        <div class="input-group col-lg-8">
            <select name="{$input.name|escape:'htmlall':'UTF-8'}" id="{$input.name|escape:'htmlall':'UTF-8'}" class="fixed-width-xl">
                {foreach $input.data_order as $k=>$value}
                    <option value="{$k|escape:'htmlall':'UTF-8'}" {if $input.value == $k}selected="selected"{/if}>{$value|escape:'htmlall':'UTF-8'}</option>
                {/foreach}
            </select>

            &nbsp;&nbsp;

            <select name="{$input.name_ad|escape:'htmlall':'UTF-8'}" id="{$input.name_ad|escape:'htmlall':'UTF-8'}" class="fixed-width-xl" style="margin-left: 10px">
                {foreach $input.data_ad as $k=>$value}
                    <option value="{$k|escape:'htmlall':'UTF-8'}" {if $input.value_ad == $k}selected="selected"{/if}>{$value|escape:'htmlall':'UTF-8'}</option>
                {/foreach}
            </select>

        </div>

    {elseif $input.type == 'text_fcom'}
        <div class="input-group col-lg-8">
            <a target="_blank" href="http://www.facebook.com/apps/application.php?id={$input.f_appid|escape:'htmlall':'UTF-8'}" class="btn btn-primary pull">{l s='Check My App' mod='blockblog'}</a>
            &nbsp;&nbsp;
            <a target="_blank" href="http://www.facebook.com/developers/editapp.php?app_id={$input.f_appid|escape:'htmlall':'UTF-8'}&amp;view=web" class="btn btn-primary pull">{l s='Configure App' mod='blockblog'}</a>
            &nbsp;&nbsp;
            <a target="_blank" href="http://developers.facebook.com/setup" class="btn btn-primary pull">{l s='Create an App' mod='blockblog'}</a>

        </div>
    {elseif $input.type == 'text_fcom_set'}
        <div class="input-group col-lg-8">


            <a target="_blank" href="http://developers.facebook.com/tools/comments?id={$input.f_appid|escape:'htmlall':'UTF-8'}&amp;view=edit_settings" class="btn btn-primary pull">{l s='Settings Comments' mod='blockblog'}</a>
            &nbsp;&nbsp;
            <a target="_blank" href="http://developers.facebook.com/tools/comments?id={$input.f_appid|escape:'htmlall':'UTF-8'}&amp;view=queue" class="btn btn-primary pull">{l s='Moderate Comments' mod='blockblog'}</a>
            &nbsp;&nbsp;
            <a target="_blank" href="http://developers.facebook.com/tools/comments?id={$input.f_appid|escape:'htmlall':'UTF-8'}" class="btn btn-primary pull">{l s='Check new comments' mod='blockblog'}</a>

        </div>
    {elseif $input.type == 'post_image_custom'}

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
                    </script>
                    {/literal}




                </div>



            </div>
            {if isset($input.desc) && !empty($input.desc)}
                <p class="help-block">
                    {$input.desc nofilter}
                    <br/>
                    <span style="color:black:font-size:13px">{l s='Max file size (upload_max_filesize) in php.ini' mod='blockblog'}: <b style="color:green">{$input.max_upload_info|escape:'htmlall':'UTF-8'}</b></span>
                    <br/>
                    <span style="color:black:font-size:13px">{l s='Max POST size (post_max_size) in php.ini' mod='blockblog'}: <b style="color:green">{$input.post_max_size|escape:'htmlall':'UTF-8'}</b></span>
                </p>
            {/if}
            {if isset($input.is_demo) && !empty($input.is_demo)}
                {$input.is_demo|escape:'quotes':'UTF-8'}
            {/if}




        </div>

    {elseif $input.type == 'group_association'}

        <div class="col-lg-8">
            <div class="panel">

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
                                <input type="checkbox" class="input_shop"
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
                <p class="help-block">
                    {$input.desc|escape:'htmlall':'UTF-8'}
                </p>
            {/if}
        </div>


    {elseif $input.type == 'group_association_author'}

        <div class="col-lg-7">
            <div class="panel">

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
                <p class="help-block">
                    {$input.desc|escape:'htmlall':'UTF-8'}
                </p>
            {/if}
        </div>

    {elseif $input.type == 'cms_pages'}

        <div class="col-lg-8">
            <div class="panel col-lg-7">


                <table width="50%" cellspacing="0" cellpadding="0" class="table">
                    <thead>
                    <tr>
                        <th>{l s='Shop' mod='blockblog'}</th>
                    </tr>
                    </thead>
                    <tbody>
                    {assign var=i value=0}
                    {foreach $input.values as $_shop}
                        <tr>
                            <td>

                                <img src="../modules/blockblog/views/img/lv2_{if count($input.values)-1 == $i}f{else}b{/if}.png" alt="{$_shop['name']|escape:'htmlall':'UTF-8'}" style="vertical-align:middle;">
                                <label class="child">
                                    <input type="checkbox" class="input_shop" value="{$_shop['id_shop']|escape:'htmlall':'UTF-8'}" name="cat_shop_association[]">
                                    {$_shop['name']|escape:'htmlall':'UTF-8'}
                                </label>
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

    {elseif $input.type == 'cms_pages_loyalty_points'}

        {assign var=cms value=$input.values}


        {if count($cms)>0}
            <div class="col-lg-4 {$input.name|escape:'htmlall':'UTF-8'}">
                <div class="panel">

                    <table class="table">

                        <tbody>

                        {foreach $cms as $key => $cms_item}
                            <tr class="alt_row">
                                <td>
                                    <div class="checkbox" style="padding-top:3px">

                                        <label for="{$key|escape:'htmlall':'UTF-8'}">{l s='1 point =' mod='blockblog'}</label>
                                    </div>

                                </td>
                                <td>
                                    <div class="col-lg-9">
                                        <div class="input-group">
                                            <input type="text" name="{$cms_item['name_item']|escape:'htmlall':'UTF-8'}[{$key|escape:'htmlall':'UTF-8'}]"
                                                   value="{$cms_item['amount']|escape:'htmlall':'UTF-8'}" />
                                            <span class="input-group-addon">&nbsp;{$cms_item['currency']|escape:'htmlall':'UTF-8'}</span>
                                        </div>
                                    </div>
                                </td>
                            </tr>
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


        {/if}
    {elseif $input.type == 'cms_pages_loyalty'}

        {assign var=cms value=$input.values}


        {if count($cms)>0}
            <div class="col-lg-4 {$input.name|escape:'htmlall':'UTF-8'}">
                <div class="panel">

                    <table class="table">
                        <thead>
                        <tr>

                            <th><b>{l s='Currency' mod='blockblog'}</b></th>
                            <th><b>{l s='Discount Amount' mod='blockblog'}</b></th>
                        </tr>
                        </thead>
                        <tbody>

                        {foreach $cms as $key => $cms_item}
                            <tr class="alt_row">
                                <td>
                                    <div class="checkbox">

                                        <label for="{$key|escape:'htmlall':'UTF-8'}">{$cms_item['name']|escape:'htmlall':'UTF-8'}</label>
                                    </div>

                                </td>
                                <td>
                                    <div class="col-lg-9">
                                        <div class="input-group">
                                            <input type="text" name="{$cms_item['name_item']|escape:'htmlall':'UTF-8'}[{$key|escape:'htmlall':'UTF-8'}]"
                                                   value="{$cms_item['amount']|escape:'htmlall':'UTF-8'}" />
                                            <span class="input-group-addon">&nbsp;{$cms_item['currency']|escape:'htmlall':'UTF-8'}</span>
                                        </div>
                                    </div>
                                </td>
                            </tr>
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


        {/if}


    {elseif $input.type == 'loyality_points_per_action'}


        <div class="col-lg-8 {$input.name|escape:'htmlall':'UTF-8'}">
            <div class="panel">



                <table class="table">
                    <thead>
                    <tr>

                        <th><b>{l s='Action' mod='blockblog'}</b></th>
                        <th><b>{l s='Points' mod='blockblog'}</b></th>
                        <th><b>{l s='How many times customer can receive points' mod='blockblog'}</b></th>
                        <th><b>{l s='Status' mod='blockblog'}</b></th>

                    </tr>
                    </thead>
                    <tbody>


                    {foreach $input.values as $key => $cms_item}
                        <tr class="alt_row">
                            <td>
                                {$cms_item.name|escape:'htmlall':'UTF-8'}

                            </td>

                            <td>
                                <div class="input-group">
                                    <input type="text" name="{$cms_item.points.name|escape:'htmlall':'UTF-8'}"
                                           value="{$cms_item.points.points|escape:'htmlall':'UTF-8'}" />
                                    <span class="input-group-addon">&nbsp;{l s='points' mod='blockblog'}</span>


                                </div>

                            </td>
                            <td style="text-align:center">
                                {if $cms_item.times.count_times != 0}
                                    <div class="input-group">
                                        <input type="text" name="{$cms_item.times.name|escape:'htmlall':'UTF-8'}"
                                               value="{$cms_item.times.times|escape:'htmlall':'UTF-8'}" />
                                        <span class="input-group-addon">&nbsp;{l s='times' mod='blockblog'}</span>


                                    </div>
                                {else}
                                    1
                                {/if}

                            </td>
                            
                            <td style="text-align:center">
                                <div class="checkbox">

                                    <label for="{$key|escape:'htmlall':'UTF-8'}">
                                        <input type="checkbox" {if $cms_item.status == $key} checked="checked"{/if}
                                               value="{$key|escape:'htmlall':'UTF-8'}" id="{$key|escape:'htmlall':'UTF-8'}"
                                               name="{$key|escape:'htmlall':'UTF-8'}"/>
                                    </label>
                                </div>

                            </td>
                        </tr>
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

    {elseif $input.type == 'wow_display_effect'}
        <div class="col-lg-3">

            {$input.value nofilter}

        </div>
    {elseif $input.type == 'checkbox_custom_store'}
        <div class="col-lg-8 {$input.name|escape:'htmlall':'UTF-8'}" style="padding: 7px">

            <input type="checkbox" name="{$input.name|escape:'htmlall':'UTF-8'}" id="{$input.name|escape:'htmlall':'UTF-8'}"
                   value="1" {if $input.values.value == 1} checked="checked"{/if} />



            {if isset($input.desc) && !empty($input.desc)}
                <p class="help-block">
                    {$input.desc|escape:'htmlall':'UTF-8'}
                </p>
            {/if}
        </div>

    {elseif $input.type == 'cms_categories'}

        <div class="col-lg-8 {$input.name|escape:'htmlall':'UTF-8'}">


            {$input.values nofilter}


            {if isset($input.desc) && !empty($input.desc)}
                <p class="help-block">
                    {$input.desc|escape:'htmlall':'UTF-8'}
                </p>
            {/if}
        </div>

    {elseif $input.type == 'text_validity'}



        <div class="col-lg-4">

            <div class="input-group">
                <input type="text" name="{$input.name|escape:'htmlall':'UTF-8'}"
                       value="{$input.value|escape:'htmlall':'UTF-8'}" />
                <span class="input-group-addon icon icon-clock-o"><b>&nbsp;day(s)</b></span>


            </div>
            {if isset($input.desc) && !empty($input.desc)}
                <p class="help-block">
                    {$input.desc|escape:'htmlall':'UTF-8'}
                </p>
            {/if}
        </div>

    {elseif $input.type == 'select_language'}

        <div class="col-lg-8">
            <div class="panel col-lg-7">


                <table width="50%" cellspacing="0" cellpadding="0" class="table">
                    <thead>
                    <tr>
                        <th>{l s='Language' mod='blockblog'}</th>
                    </tr>
                    </thead>
                    <tbody>
                    {assign var=i value=0}
                    {foreach $input.values as $_shop}
                        <tr>
                            <td>

                                <label class="child">
                                    <input type="radio" value="{$_shop['id_lang']|escape:'htmlall':'UTF-8'}" id="select_language" name="select_language"/>
                                    &nbsp;&nbsp;<img src="../img/l/{$_shop['id_lang']|escape:'htmlall':'UTF-8'}.jpg">&nbsp;{$_shop['name']|escape:'htmlall':'UTF-8'}
                                </label>
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

    {elseif $input.type == 'related_categories_my'}

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
                <div style="clear:both"></div>
                <p class="help-block">
                    {$input.desc|escape:'htmlall':'UTF-8'}
                </p>
            {/if}
        </div>

    {elseif $input.type == 'text_autopost'}
        <div class="col-lg-{if isset($input.col)}{$input.col|intval}{else}9{/if}{if !isset($input.label)} col-lg-offset-3{/if}">


            {if isset($input.lang) AND $input.lang}

                {if $languages|count > 1}
                    <div class="form-group">
                {/if}
                {foreach $languages as $language}
                    {assign var='value_text' value=$fields_value[$input.name][$language.id_lang]}
                    {if $languages|count > 1}
                        <div class="translatable-field lang-{$language.id_lang|escape:'htmlall':'UTF-8'}" {if $language.id_lang != $defaultFormLanguage}style="display:none"{/if}>
                        <div class="col-lg-9">
                    {/if}
                    {if strlen($input.text_before)>0}
                        <span style="float:left;margin:7px 5px 0 0;font-weight: bold">{$input.text_before|escape:'htmlall':'UTF-8'}</span>
                    {/if}
                    <input type="text" style="float:left;margin-right:5px;width:40%"
                           id="{if isset($input.id)}{$input.id|escape:'htmlall':'UTF-8'}_{$language.id_lang|escape:'htmlall':'UTF-8'}{else}{$input.name|escape:'htmlall':'UTF-8'}_{$language.id_lang|escape:'htmlall':'UTF-8'}{/if}"
                           name="{$input.name|escape:'htmlall':'UTF-8'}_{$language.id_lang|escape:'htmlall':'UTF-8'}"
                           class="{if isset($input.class)}{$input.class|escape:'htmlall':'UTF-8'}{/if}{if $input.type == 'tags'} tagify{/if}"
                           value="{if isset($input.string_format) && $input.string_format}{$value_text|string_format:$input.string_format|escape:'htmlall':'UTF-8'}{else}{$value_text|escape:'htmlall':'UTF-8'}{/if}"
                           onkeyup="if (isArrowKey(event)) return ;updateFriendlyURL();"
                            {if isset($input.size)} size="{$input.size|escape:'htmlall':'UTF-8'}"{/if}
                            {if isset($input.maxchar) && $input.maxchar} data-maxchar="{$input.maxchar|intval}"{/if}
                            {if isset($input.maxlength) && $input.maxlength} maxlength="{$input.maxlength|intval}"{/if}
                            {if isset($input.readonly) && $input.readonly} readonly="readonly"{/if}
                            {if isset($input.disabled) && $input.disabled} disabled="disabled"{/if}
                            {if isset($input.autocomplete) && !$input.autocomplete} autocomplete="off"{/if}
                            {if isset($input.required) && $input.required} required="required" {/if}
                            {if isset($input.placeholder) && $input.placeholder} placeholder="{$input.placeholder|escape:'htmlall':'UTF-8'}"{/if} />
                    <span style="float:left;margin:7px 5px 0 0;font-weight: bold">{$input.text_after|escape:'htmlall':'UTF-8'}</span>
                    {if $languages|count > 1}
                        </div>
                        <div class="col-lg-2">
                            <button type="button" class="btn btn-default dropdown-toggle" tabindex="-1" data-toggle="dropdown">
                                {$language.iso_code|escape:'htmlall':'UTF-8'}
                                <i class="icon-caret-down"></i>
                            </button>
                            <ul class="dropdown-menu">
                                {foreach from=$languages item=language}
                                    <li><a href="javascript:hideOtherLanguage({$language.id_lang|escape:'htmlall':'UTF-8'});" tabindex="-1">{$language.name|escape:'htmlall':'UTF-8'}</a></li>
                                {/foreach}
                            </ul>
                        </div>
                        </div>
                    {/if}
                {/foreach}




                {if $languages|count > 1}
                    </div>
                {/if}

            {/if}

            {if isset($input.desc) && !empty($input.desc)}
                <p class="help-block">
                    {$input.desc|escape:'htmlall':'UTF-8'}
                </p>
            {/if}
        </div>

    {elseif $input.type == 'sitemap_limit'}
        <div class="input-group col-lg-2">
            <input type="text" name="{$input.name|escape:'htmlall':'UTF-8'}" id="{$input.name|escape:'htmlall':'UTF-8'}"
                   value="{$input.value|escape:'htmlall':'UTF-8'}" />
            <span class="input-group-addon">&nbsp;Urls/xml</span>



        </div>

    {elseif $input.type == 'custom_rss_ursl'}
        <div class="input-group col-lg-2">


            {$input.html nofilter}


        </div>

    {elseif $input.type == 'text_custom_order_statuses_loyality'}

        {assign var=cms value=$input.value}

        <div class="col-lg-6">

            <div class="panel">

                <table class="table">
                    <thead>
                    <tr>

                        <th>&nbsp;</th>
                        <th><b>{l s='Order status' mod='blockblog'}</b></th>
                    </tr>
                    </thead>
                    <tbody>

                    {foreach $cms as $key => $cms_item}
                        <tr class="alt_row">
                            <td>

                                <div class="input-group">
                                    <input type="checkbox" name="{$input.name nofilter}[]"
                                            {foreach $input.orderstatuses as $id_status}
                                                {if $id_status == $cms_item['id_order_state']}
                                                    checked="checked"
                                                {/if}

                                            {/foreach}
                                           value="{$cms_item['id_order_state']|escape:'htmlall':'UTF-8'}" />
                                </div>
                            </td>
                            <td>


                                        <span style="background-color:{$cms_item['color']|escape:'htmlall':'UTF-8'};color:white;padding:4px;border-radius:5px;line-height:25px;margin:3px 0">
                                            {$cms_item['name']|escape:'htmlall':'UTF-8'}
                                        </span>
                            </td>
                        </tr>
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





{block name="input_row"}

    {if $input.type=='positions_blocks_order_by'}
        {assign var=mobile_txt value=$input.mobile_txt}




        <ul id="sidebar-positions-title" class="sidebar-positions">
            <li>
                <div>
                    <span class="title-order-by-blog">&nbsp;{l s='Block' mod='blockblog'}</span>
                                    <span class="wrapper-title-order-by">

                                        <span class="title-item-order-by">
                                            {l s='Desktop' mod='blockblog'}
                                        </span>

                                        <span class="title-item-order-by">
                                            {l s='Mobile' mod='blockblog'}
                                        </span>

                                    </span>
                    <div class="clear"></div>
                </div>

            </li>
        </ul>

        <ul id="sidebar-positions-blockblog" class="sidebar-positions sidebar-positions-{$input.position_key|escape:'htmlall':'UTF-8'}" data-item="{$input.position_key|escape:'htmlall':'UTF-8'}">
            {assign var=i_pos value=1}


            {foreach from =$input.positions_blocks_blog key='key_item' item='data_item'}
                <li id="sidebar-position-{$data_item['position_current_pos']}-{$data_item['prefix']}-{$data_item['orderby']|escape:'htmlall':'UTF-8'}" data-prefix="{$data_item['prefix']|escape:'htmlall':'UTF-8'}">

                    <div>
                        <div class="title-sidebar">
                    <span class="position_number" >
                        <span>
                            {$data_item['orderby']|escape:'htmlall':'UTF-8'}
                        </span>
                    </span>

                            <div class="float-left blockblog-blog">

                                {$data_item['name']|escape:'htmlall':'UTF-8'}

                                <div class="clear"></div>

                            </div>

                        </div>

                        {assign var=pos_desktop value=$data_item['position_current_pos']|cat:$data_item['prefix']}



                        <label class="blockblog-blog-desktop {$data_item['position_current_pos']}_{$data_item['prefix']}_desktop blockblog_on_off_switch {if $data_item['position_current_blog'] == $pos_desktop} active{/if}"  data-blockblog-blog="{$data_item['position_current_pos']}_{$data_item['prefix']}_desktop">
                            <input class="blockblog_on_off_slider" type="checkbox" {if $data_item['position_current_blog'] == $pos_desktop} checked ="checked"{/if} value="1" />
                            <span class="blockblog_on_off_slider_label on">{l s='Enable' mod='blockblog'}</span>
                            <span class="blockblog_on_off_slider_label off">{l s='Disable' mod='blockblog'}</span>
                        </label>

                        {assign var=pos_mobile value=$pos_desktop|cat:$mobile_txt}
                        {assign var=pos_mobile_current value=$data_item['position_current_pos_mobile']}




                        <label class="blockblog-blog-mobile {$pos_mobile_current}_{$data_item['prefix']}_mobile blockblog_on_off_switch {if $data_item['position_current_blog_mobile'] == $pos_mobile} active{/if}" data-blockblog-blog="{$pos_mobile_current}_{$data_item['prefix']}_mobile">
                            <input class="blockblog_on_off_slider" type="checkbox" {if $data_item['position_current_blog_mobile'] == $pos_mobile} checked ="checked"{/if} value="1" />
                            <span class="blockblog_on_off_slider_label on">{l s='Enable' mod='blockblog'}</span>
                            <span class="blockblog_on_off_slider_label off">{l s='Disable' mod='blockblog'}</span>
                        </label>
                    </div>
                </li>




            {/foreach}


        </ul>

    {literal}
        <script type="text/javascript">

            var ajax_link_blockblog = '{/literal}{$input.ajax_url nofilter}{literal}';
            var token_blockblog = '{/literal}{$input.token_custom|escape:'htmlall':'UTF-8'}{literal}';

            var success_message_blockblog = '{/literal}{$input.success_message|escape:'htmlall':'UTF-8'}{literal}';
            var error_message_blockblog = '{/literal}{$input.error_message|escape:'htmlall':'UTF-8'}{literal}';

            var is17_blockblog = '{/literal}{$input.is17|escape:'htmlall':'UTF-8'}{literal}';

            $(document).ready(function(){

                $( ".sidebar-positions{/literal}-{$input.position_key|escape:'htmlall':'UTF-8'}{literal}" ).sortable();


                if($('.sidebar-positions{/literal}-{$input.position_key|escape:'htmlall':'UTF-8'}{literal}').length)
                {
                    var $sidebar_positions_blockblog = $(".sidebar-positions{/literal}-{$input.position_key|escape:'htmlall':'UTF-8'}{literal}");
                    $sidebar_positions_blockblog.sortable({
                        opacity: 0.6,
                        handle: ".position_number",
                        start: function(e, ui) {
                            // creates a temporary attribute on the element with the old index

                        },
                        update: function(e, ui) {



                            var orderby_serialize = $(this).sortable("serialize");
                            //console.log(orderby_serialize);return;

                            $.post(ajax_link_blockblog, {
                                        token: token_blockblog,
                                        ajax : true,
                                        controller : 'AdminBlockblogajax',
                                        action : 'BlockblogAjax',

                                        action_custom: 'orderbypositionblog',

                                        orderby_serialize:orderby_serialize,


                                    },
                                    function (data) {
                                        if (data.status == 'success') {

                                            showSaveMessage('success');
                                            var i=1;

                                            $('.sidebar-positions{/literal}-{$input.position_key|escape:'htmlall':'UTF-8'}{literal} li').each(function(){

                                                $(this).find('.position_number').html('<span>'+i+'</span>');
                                                //var data_prefix_current = $(this).find('.position_number').parent().parent().parent().attr('data-prefix');
                                                //$(this).find('.position_number').parent().parent().parent().attr('id','sidebar-position-{/literal}{$input.position_key|escape:'htmlall':'UTF-8'}{literal}-'+data_prefix_current+'-'+i);
                                                i++;
                                            });

                                        } else {
                                            showSaveMessage('error');
                                        }

                                    }, 'json');


                        },
                        stop: function( event, ui ) {
                        }
                    });
                }
            });

        </script>
    {/literal}





    {else}
        {$smarty.block.parent}
    {/if}

{/block}