{*
* 2007-2017 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2023 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{extends file="helpers/form/form.tpl"}

{block name="label"}
    {if $input.type == 'cat_list' || $input.type == 'button_activator' || $input.type == 'shop_switch' || $input.type == 'submit_text_button' || $input.type == 'faq_tmp_list' || $input.type == 'info_text'} 
    {else}
	   {$smarty.block.parent}
    {/if}
{/block}

{block name="legend"}
    <h3>
        {if isset($field.image)}<img src="{$field.image|escape:'html':'UTF-8'}" alt="{$field.title|escape:'html':'UTF-8'}" />{/if}
        {if isset($field.icon)}<i class="{$field.icon|escape:'html':'UTF-8'}"></i>{/if}
        {$field.title|escape:'html':'UTF-8'}
        {if !isset($field.class)}
        <span class="panel-heading-action">
			{foreach from=$toolbar_btn item=btn key=k name=toolbar}
				{if $k != 'modules-list' && $k != 'back'}
					{if isset($btn.free_cat) && count($btn.free_cat) > 0}
						<a id="desc-{$table|escape:'html':'UTF-8'}-{if isset($btn.imgclass)}{$btn.imgclass|escape:'html':'UTF-8'}{else}{$k|escape:'html':'UTF-8'}{/if}" class="list-toolbar-btn" {if isset($btn.href)}href="{$btn.href}"{/if} {if isset($btn.target) && $btn.target}target="_blank"{/if}{if isset($btn.js) && $btn.js}onclick="{$btn.js|escape:'html':'UTF-8'}"{/if}>
							<span title="" data-toggle="tooltip" class="label-tooltip" data-original-title="{$btn.desc|escape:'html':'UTF-8'}" data-html="true">
								<i class="process-icon-{if isset($btn.imgclass)}{$btn.imgclass|escape:'html':'UTF-8'}{else}{$k|escape:'html':'UTF-8'}{/if} {if isset($btn.class)}{$btn.class|escape:'html':'UTF-8'}{/if}" ></i>
							</span>
						</a>
						
					    <script type="text/javascript">
				            var category_empty_list = 1;
        				</script>
					{else if isset($btn.no_free_cat) && $btn.no_free_cat==1}
						<a id="desc-{$table|escape:'html':'UTF-8'}-{if isset($btn.imgclass)}{$btn.imgclass|escape:'html':'UTF-8'}{else}{$k|escape:'html':'UTF-8'}{/if}" class="list-toolbar-btn" {if isset($btn.href)}href="{$btn.href}"{/if} {if isset($btn.target) && $btn.target}target="_blank"{/if}{if isset($btn.js) && $btn.js}onclick="{$btn.js|escape:'html':'UTF-8'}"{/if}>
							<span title="" data-toggle="tooltip" class="label-tooltip" data-original-title="{$btn.desc|escape:'html':'UTF-8'}" data-html="true">
								<i class="process-icon-{if isset($btn.imgclass)}{$btn.imgclass|escape:'html':'UTF-8'}{else}{$k|escape:'html':'UTF-8'}{/if} {if isset($btn.class)}{$btn.class|escape:'html':'UTF-8'}{/if}" ></i>
							</span>
						</a>
					{/if}
				{/if}
			{/foreach}
			</span>
            {elseif (isset($field.class) && ($field.class == 'faq_blck_edit' || $field.class == 'faq_blck_add'))}
            <span class="panel-heading-action">
			     <a id="desc-tmp-blck" class="list-toolbar-btn" href="{$field.href}" >
			         <span title="{$field.desc|escape:'html':'UTF-8'}" data-toggle="tooltip" class="label-tooltip" data-original-title="{$field.desc|escape:'html':'UTF-8'}" data-html="true">
			         <i class="process-icon-new" ></i>
			         </span>
			     </a>
			</span>
            {elseif (isset($field.class) && ($field.class == 'faq_cat_list' && isset($display) && $display == 'edit'))}
                <script>
                    let copyTmpTxt = "{l s='Copy FAQ from template' mod='addcatdesc'}";
                    let errTmpTxt = "{l s='Please, choose the template' mod='addcatdesc'}";
                    let copyTxt = "{l s='Copying...' mod='addcatdesc'}";

                    $(document).ready(function() {   
                        $('#copy-tmp-blck').fancybox({
                            "padding" : 20,
                            "imageScale" : false, 
                            "zoomOpacity" : false,
                            "zoomSpeedIn" : 1000,	
                            "zoomSpeedOut" : 1000,	
                            "zoomSpeedChange" : 1000, 
                            "frameWidth" : 700,	 
                            "frameHeight" : 600, 
                            "overlayShow" : true, 
                            "overlayOpacity" : 0.8,	
                            "hideOnContentClick" :false,
                            "centerOnScroll" : false,
                            "title" : '',
                            'height': 'auto',
                            'width' : '450',
                            'autoSize': false
                        });

                        selTemplateCopy();
                    }); 

                    function selTemplateCopy(){
                        if ($('#sel_template').length > 0) {
                            $('#sel_template option').clone().appendTo('#copy_template').removeAttr('selected');
                            $('#button_copy_wrapper').html('<button id="form_submit_copy_stay_btn" type="submit" name="submitCopyTmpCategoryStay" class="btn btn-default">' + copyTmpTxt + '</button>');
                            $("#form_submit_copy_stay_btn").click(function() { 
                                let selEl = $('#copy_template').val();
                                let idText = {$id_text|intval};
                                if (selEl != null && selEl > 0) {
                                    $("#form_submit_copy_stay_btn").replaceWith("<div id='sending_proc'>" + copyTxt + "</div>");
                                    
                                    $.ajax({
                                            type: 'GET',
                                            url: '{$module_url}&ajax=true&secure_key={$secure_key|escape:"html":"UTF-8"}',
                                            data: 'action=copyTmpToCat&id_blck=' + selEl + '&id_text=' + idText,
                                            dataType : "json",
                                            async: true,
                                            cache: false,
                                            success: function(data) {
                                                if (data.success == true) {
                                                    $("#sending_proc").replaceWith("<div id='success_res'>" + data.num_rows + "</div>");
                                                    location.reload();
                                                } else {
                                                    $("#sending_proc").replaceWith("<div id='error_res'>" + data.warning + "</div>");
                                                }
                                            }
                                    });
                                } else {
                                    alert(errTmpTxt);
                                }
                            });
                        }
                    }

                </script>
                <style>
                    #copy_template {
                        width:100%;
                        padding-top:15px;
                    }
                    #button_copy_wrapper {
                        margin-top:20px;
                        margin-bottom:5px;
                        text-align:center;
                    }

                    #form_submit_copy_stay_btn {
                        color: #fff;
                        background: #25b9d7;
                        border-color: #25b9d7;
                    }

                    #form_submit_copy_stay_btn:hover {
                        color: #fff;
                        background: #7cd5e7;
                        border-color: #7cd5e7;
                    }
                    
                </style>
            <div id="sel_tpl_wrapper" class="sel_tpl_wrapper bootstrap" style="display: none;'">
                <h3 style="text-align:center;">{l s='Choose template from the list to copy FAQ' mod='addcatdesc'}</h3>
                <div id="sel_template_copy">
                    <select class="form-control" id="copy_template" name="copy_template[]" size="10">
                    </select>
                    <div id="button_copy_wrapper">
                    </div>
                </div>
            </div>    
            <span class="panel-heading-action">
                {if isset($tmp_name_sel)}
                    <a id="copy-tmp-blck" class="list-toolbar-btn" href="#sel_tpl_wrapper"><span title="{l s='Copy FAQ from template' mod='addcatdesc'}" data-toggle="tooltip" class="label-tooltip" data-original-title="{$field.desc|escape:'html':'UTF-8'}" data-html="true"><i class="icon-copy" ></i></span></a>
                {/if}
                <a id="desc-tmp-blck" class="list-toolbar-btn" href="{$field.href}" >
                    <span title="{$field.desc|escape:'html':'UTF-8'}" data-toggle="tooltip" class="label-tooltip" data-original-title="{$field.desc|escape:'html':'UTF-8'}" data-html="true">
                    <i class="process-icon-new" ></i>
                    </span>
                </a>
           </span>                

            {elseif (isset($field.class) && $field.class == 'noshow') && isset($new_version178) && ($new_version178 > 0)}
            <style>
            #fieldset_3_3 .caret_wrapper {
                float:right;
                padding-right:5px;
                cursor: pointer;
            }
            #fieldset_3_3 .caret_wrapper .caret{
                
                margin: 5px;
                transform: rotate(180deg);
            }
            
            #fieldset_3_3 .caret_wrapper .caret.rotate_caret {
                transform: rotate(0deg);
            }
            
            #fieldset_3_3.panel .form-wrapper {
                display: none;
            }
            </style>
            <script>
            function hideInstWrap() {
                if ($('#fieldset_3_3.panel .form-wrapper').hasClass('showed')) {
                    $('#fieldset_3_3.panel .form-wrapper').hide().removeClass('showed');
                    $('#fieldset_3_3.panel .caret_wrapper .caret').removeClass('rotate_caret');
                } else {
                    $('#fieldset_3_3.panel .form-wrapper').show().addClass('showed');    
                    $('#fieldset_3_3.panel .caret_wrapper .caret').addClass('rotate_caret');
                }
                
            }
            
            </script>
            <span class="caret_wrapper" onclick="hideInstWrap();">
                <span class="caret"></span>
            </span> 
            {elseif (isset($field.class) && $field.class == 'noshowforinfo') && isset($new_version178) && ($new_version178 > 0)}            
                <span>
                </span> 
			{/if}

    </h3>
{/block}

{block name="input"}
    {if isset($ch_css)}
    {else}
    <link rel="stylesheet" href="{$smarty.const._MODULE_DIR_|escape:'html':'UTF-8'}addcatdesc/views/css/addcatdesc_admin.css">
    {/if}
    {assign var=ch_css value=1}
    
	{if $input.type == 'cat_list'}
	<div class="row" style="width:100%;">
        <script>
            var come_from = '{$name_controller|escape:'html':'UTF-8'}';
            var token = '{$token|escape:'html':'UTF-8'}';
            var alternate = 1;
            var str = "{l s='Are you sure want to delete record with id = ' mod='addcatdesc'}";
            var strBoxes = "{l s='Are you sure want to delete records' mod='addcatdesc'}";
            
            function delConfirm(id){
         		if (confirm(str+id+"?"))
                	location.href = this.getAttribute("href");
			}

            function delBoxes(){
                if ($(".descBox").is(':checked')) {
                    if (confirm(strBoxes + "?")) {
                        $("#deleteboxhidden").val(1);
                        $('#configuration_form').submit();    
                    }
                }
            }

            function activeBoxes(ch){
                if ($(".descBox").is(':checked')) {
                    (ch === true ? $("#activeboxhidden").val(1) : $("#activeboxhidden").val(2));
                    $('#configuration_form').submit();
                }
            }

            function checkBoxes(ch) {
                $('.descBox').prop('checked', ch);
            }
            function checkFilled(key) {
                let valSearch = $('#filter_column_name').val();
                if (valSearch) {
                    $('#obj_filter_submit').removeAttr('disabled');
                } else {
                    $('#obj_filter_submit').attr('disabled', 'true');
                }
            }
            function LangChanged() {
                $('#lang_submit').click(); 
            }
            
        </script>
        
        {if (isset($new_version180) && $new_version180 > 0)}
            <script>
                $(document).ready(function(){
                    $("input.color-small").parents('div.col-lg-6').first().css("width", "200");
                });
            </script>
        {/if}
        
        <style>
            #cat_list .search-button, #cat_list .cancel-button {
                float: right;
                cursor: pointer;
                margin-right: 5px;
            }
            
            #cat_list .search-button i, #cat_list .cancel-button i {
                float: left;
                font-size: 18px;
            }
            
            #cat_list .search-name-wrapper {
                text-align:center;
            }
            
            #cat_list .search-name-wrapper input{
                width: 80%;
                display: inline-block;
            }
            #lang_submit {
                display: none;
            }
            
        </style>
        {if (isset($new_version178) && $new_version178 > 0)}
        <style>
        #fieldset_1_1 .col-lg-8, #fieldset_2_2 .col-lg-8{
            width:100%;
        }
        #fieldset_1_1 .form-wrapper{
            max-width:100%;
        }
        #fieldset_1_1 button.dropdown-toggle {
            display: inline-block !important;    
        }
        {if (isset($new_version180) && $new_version180 > 0)}
            //added to js code
        {else}
            #fieldset_0 .color-small {
                width:100px !important;
            }
        {/if}
        .form-group label{
            text-align:right !important;
        }
        
        .bootstrap label.control-label span.label-tooltip {
            background-color: #edf7fb;
            border-bottom: 1px solid #d8edf7;
            border-radius: 3px;
        }
        </style>
        {/if}
        {assign var=category_list value=$input.values}
        {if (isset($category_list) && count($category_list) > 0) || {$filter_column_name != ''}}
        <div>
            {if isset($language_arr) && count($language_arr) > 1}
                <div class="lang_selection row">
                    <label for="lang_arr_sel" class="col-lg-6">{l s='Select language for list:' mod='addcatdesc'}</label>
                    <select name="lang_arr_sel" class="col-lg-6" onchange="LangChanged();">
                        {foreach $language_arr as $key => $lang_el}
                            <option {if $lang_el['id_lang'] == $language_cur}selected{/if}  value="{$lang_el['id_lang']|intval}">{$lang_el['name']|escape:'htmlall':'UTF-8'}</option>
                        {/foreach}
                    </select>
                    <button type="submit" id="lang_submit" name="langSubmit"></button>
                </div>
            {/if}
                <div>
                    <table cellspacing="0" cellpadding="0" style="min-width:40em;" class="table tableDnD cat" id="cat_list">
                        <thead>
                            <tr class="nodrag nodrop">
                                <th {if $multishop == true}style="width:10px;"{/if}>--</th>
                                {if $multishop == true}
                                    <th class="shop_id" style="width:30px; text-align:center;">{l s='ID_Shop' mod='addcatdesc'}</th>
                                {/if}
                                <th class="text_id">{l s='ID_Text' mod='addcatdesc'} {if isset($smarty.get.orderIdText)}{if $smarty.get.orderIdText=='ASC'}<i class="icon-caret-down active"></i> <a href="{$link->getAdminLink('AdminModules')|escape:'htmlall':'UTF-8'}&configure=addcatdesc&orderIdText=DESC"><i class="icon-caret-up"></i></a>{elseif $smarty.get.orderIdText=='DESC'}<a href="{$link->getAdminLink('AdminModules')|escape:'htmlall':'UTF-8'}&configure=addcatdesc&orderIdText=ASC"><i class="icon-caret-down"></i></a> <i class="icon-caret-up active"></i>{/if}{else}<a href="{$link->getAdminLink('AdminModules')|escape:'htmlall':'UTF-8'}&configure=addcatdesc&orderIdText=ASC"><i class="icon-caret-down"></i></a> <a href="{$link->getAdminLink('AdminModules')|escape:'htmlall':'UTF-8'}&configure=addcatdesc&orderIdText=DESC"><i class="icon-caret-up"></i></a>{/if}</th>
                                <th class="cat_id_name">{l s='ID_Cat' mod='addcatdesc'} {if isset($smarty.get.orderIdCat)}{if $smarty.get.orderIdCat=='ASC'}<i class="icon-caret-down active"></i> <a href="{$link->getAdminLink('AdminModules')|escape:'htmlall':'UTF-8'}&configure=addcatdesc&orderIdCat=DESC"><i class="icon-caret-up"></i></a>{elseif $smarty.get.orderIdCat=='DESC'}<a href="{$link->getAdminLink('AdminModules')|escape:'htmlall':'UTF-8'}&configure=addcatdesc&orderIdCat=ASC"><i class="icon-caret-down"></i></a> <i class="icon-caret-up active"></i>{/if}{else}<a href="{$link->getAdminLink('AdminModules')|escape:'htmlall':'UTF-8'}&configure=addcatdesc&orderIdCat=ASC"><i class="icon-caret-down"></i></a> <a href="{$link->getAdminLink('AdminModules')|escape:'htmlall':'UTF-8'}&configure=addcatdesc&orderIdCat=DESC"><i class="icon-caret-up"></i></a>{/if} - {l s='Category name' mod='addcatdesc'} {if isset($smarty.get.orderCatName)}{if $smarty.get.orderCatName=='ASC'}<i class="icon-caret-down active"></i> <a href="{$link->getAdminLink('AdminModules')|escape:'htmlall':'UTF-8'}&configure=addcatdesc&orderCatName=DESC"><i class="icon-caret-up"></i></a>{elseif $smarty.get.orderCatName=='DESC'}<a href="{$link->getAdminLink('AdminModules')|escape:'htmlall':'UTF-8'}&configure=addcatdesc&orderCatName=ASC"><i class="icon-caret-down"></i></a> <i class="icon-caret-up active"></i>{/if}{else}<a href="{$link->getAdminLink('AdminModules')|escape:'htmlall':'UTF-8'}&configure=addcatdesc&orderCatName=ASC"><i class="icon-caret-down"></i></a> <a href="{$link->getAdminLink('AdminModules')|escape:'htmlall':'UTF-8'}&configure=addcatdesc&orderCatName=DESC"><i class="icon-caret-up"></i></a>{/if}</th>
                                <th>{l s='Additional content' mod='addcatdesc'}</th>
                                <th style="text-align:center;">{l s='FAQ' mod='addcatdesc'}</th>
                                <th style="text-align:center;">{l s='Active' mod='addcatdesc'}</th>
                                <th style="text-align:left;width:100px;">{l s='Actions' mod='addcatdesc'}</th>
                            </tr>
                            <tr class="search_wrapper nodrag nodrop" id="search_wrapper">
                                <th></th>
                                {if $multishop == true}<th></th>{/if}
                                <th></th>
                                <th class="search-name-wrapper">
                                    <input id="filter_column_name" onkeyup="checkFilled(event.key)" type="text" class="form-control" placeholder="{l s='Search by name' mod='addcatdesc'}" name="filter_column_name" value="{$filter_column_name|escape:'htmlall':'UTF-8'}">
                                </th>
                                <th></th>
                                <th colspan="3">
                                <button type="submit" class="btn cancel-button" id="obj_cancel_submit" name="objCancelSubmit" title="{l s='Cancel' mod='addcatdesc'}" {if empty($filter_column_name)} disabled="" {/if}>
                                    <i class="material-icons">cancel</i>
                                    </button>
                                <button type="submit" class="btn btn-primary search-button" id="obj_filter_submit" name="objFilterSubmit" title="Search" {if empty($filter_column_name)} disabled="" {/if}>
                                    <i class="material-icons">search</i>
                                    {l s='Search' mod='addcatdesc'}
                                    </button>
                                </th>
                            </tr>                              
                        </thead>
                        <tbody>
                           {if (count($category_list) > 0)}
                           {foreach $category_list as $key => $category} 
                                <tr id="tr_{$category['id_text']|intval}">
                                    <td><input type="checkbox" class="descBox" name="descBox[]" value="{$category['id_text']|intval}"/> </td>
                                    {if $multishop == true}
                                        <td style="text-align:center;"><div class="faq_tooltip"><span>{$category['id_shop']|intval}</span><div class="tooltiptext">{$category['shop_name']|escape:'html':'UTF-8'}</div></div></td>
                                    {/if}
                                    <td>{$category['id_text']|intval}</td>
                                    <td class="border_lr">{$category['id_category']|intval} - {$category['name']|escape:'html':'UTF-8'}</td>
                                    <td>{$category['content']|truncate:250:'...'|escape:'html':'UTF-8'}</td>
                                    <td class="border_lr" style="text-align: center;">{if isset($category['linked_blck']) && count($category['linked_blck'])>0}<div class="faq_tooltip"><span>{$category['cnt_faq']|intval}</span><div class="tooltiptext">{foreach $category['linked_blck'] as $l => $block}<div>{$block['title']|escape:'html':'UTF-8'}</div>{/foreach}</div></div>{else}{$category['cnt_faq']|intval}{/if}</td>
                                    <td style="text-align:center;">
                                    {if $category['active'] == 1}
								        <a class="btn btn-success" href="{$link->getAdminLink('AdminModules')|escape:'htmlall':'UTF-8'}&configure=addcatdesc&changeStatus&id_text={$category['id_text']|intval}&act=true">
								            <i class="icon-check"></i>
								        </a>
								    {else}
								        <a class="btn btn-danger" href="{$link->getAdminLink('AdminModules')|escape:'htmlall':'UTF-8'}&configure=addcatdesc&changeStatus&id_text={$category['id_text']|intval}&act=false">
								            <i class="icon-remove"></i>
								        </a>
                                    {/if}
                                    </td>
                                    <td class="text-right">
                                        <div class="btn-group-action">
                                        <div class="btn-group pull-right">
                            			     <a class="edit btn btn-default" href="{$link->getAdminLink('AdminModules')|escape:'htmlall':'UTF-8'}&configure=addcatdesc&editCategoryText&id_cat={$category['id_category']|intval}&id_shop_cat={$category['id_shop']|intval}" title="{l s='Edit' mod='addcatdesc'}" title="{l s='Edit' mod='addcatdesc'}">
                         						<i class="icon-pencil"></i>
                      						    {l s='Edit' mod='addcatdesc'}
                        			         </a>
                                 			<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                            				<span class="caret"></span>
                             			    </button>
                                 			<ul class="dropdown-menu">
                                                <li>
                                                    <a href="{$category['link']|escape:'html':'UTF-8'}" title="{l s='See results in frontoffice' mod='addcatdesc'} {l s='(Opens in new window)' mod='addcatdesc'}" target="_blank" >
                                                    <i class="icon-search"></i> 
                                                    {l s='See results' mod='addcatdesc'}
                                                    </a>
                                				</li>
                                                <li>
                                                    <a href="{if ($new_version == 1)} {$link->getAdminLink('AdminCategories', true, ['id_category' => $category['id_category'], 'updatecategory' => 1])|escape:'htmlall':'UTF-8'} {else} {$link->getAdminLink('AdminCategories')|escape:'htmlall':'UTF-8'}&id_category={$category['id_category']|intval}&updatecategory {/if}" title="{l s='See category page in backoffice' mod='addcatdesc'} {l s='(Opens in new window)' mod='addcatdesc'}" target="_blank" >
                                                    <i class="icon-search-plus"></i> 
                                                    {l s='See in admin panel' mod='addcatdesc'}
                                                    </a>
                                				</li>
                                                <li class="divider"></li>
                                                <li>
                               					    <a href="{$link->getAdminLink('AdminModules')|escape:'htmlall':'UTF-8'}&configure=addcatdesc&deleteCategoryText&id_text={$category['id_text']|intval}" title="{l s='Delete' mod='addcatdesc'}" onclick="delConfirm({$category['id_text']|intval}); return false;">
                                  						<i class="icon-trash"></i>
                                  						{l s='Delete' mod='addcatdesc'}
                                   					</a>
                                				</li>
                                 			</ul>
                                        </div>
        		                        </div>
                                    </td>
                                </tr>
                            {/foreach}
                            {else}
                                <tr><td colspan="8" style="text-align:center;">{l s='Empty results' mod='addcatdesc'}</td></tr>
                            {/if}
                        </tbody>
                    </table>
                    {if $notpage != 1}
				    	<div id="cat_pagination">
				    		{if isset($smarty.get.p)}
				    			{assign var='getP' value=$smarty.get.p}
				    		{else}
					    		{assign var='getP' value=0}
				    		{/if}
				    		{foreach from=$pages item=page name=pages key=k}
				   				{if $getP != $k+1 and ($getP != '' or $k!=0)}
				   					<a id="page_link_{$k|intval+1}" href="{$page|escape:'html':'UTF-8'}">{$k|intval+1}</a>
				    			{else}
				    				<span class="current_pos">{$k|intval+1}</span>
				    			{/if}	
				    		{/foreach}
				    	</div>
					{/if}
                </div>
                {if (count($category_list) > 0)}
                <div class="row">
                    <input type="hidden" id="deleteboxhidden" name="deleteboxhidden" value="0"/>
                    <input type="hidden" id="activeboxhidden" name="activeboxhidden" value="0"/>
                    
                            	<div class="col-lg-6">
                            		<div class="btn-group bulk-actions dropup">
                            			<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                            				{l s='Bulk actions' mod='addcatdesc'} <span class="caret"></span>
                            			</button>
                            			<ul class="dropdown-menu">
                            				<li>
                            					<a href="#" onclick="checkBoxes(true);return false;">
                            						<i class="icon-check-sign"></i>&nbsp;{l s='Select all' mod='addcatdesc'}
                            					</a>
                            				</li>
                            				<li>
                            					<a href="#" onclick="checkBoxes(false);return false;">
                            						<i class="icon-check-empty"></i>&nbsp;{l s='Unselect all' mod='addcatdesc'}
                            					</a>
                            				</li>
                            				<li class="divider"></li>
                                            <li>
												<a href="#" onclick="activeBoxes(true);return false;">
							                         <i class="icon-power-off text-success"></i>&nbsp;{l s='Turn on selected' mod='addcatdesc'}
						                        </a>
											</li>
                                            <li>
												<a href="#" onclick="activeBoxes(false);return false;">
							                         <i class="icon-power-off text-danger"></i>&nbsp;{l s='Turn off selected' mod='addcatdesc'}
						                        </a>
											</li>
                                            <li class="divider"></li>
                            					<li>
                            						<a href="#" onclick="delBoxes();return false;">
                            							<i class="icon-trash"></i>&nbsp;{l s='Delete selected' mod='addcatdesc'}
                            						</a>
                            					</li>
                            			</ul>
                            		</div>
	                           </div>
                    </div>
                    {/if}
        </div>
        {else}
        	<h2 style="margin-top:1px;margin-bottom:-10px;text-align:center;">{l s='You have not any descriptions for categories. Add new one.' mod='addcatdesc'}</h2>
        {/if}
	</div>
    {elseif $input.type == 'faq_tmp_list'}
    {if (isset($new_version178) && $new_version178 > 0)}
    <style>
        #fieldset_1_1 .form-wrapper{
            max-width:100%;
        }
        #fieldset_1_1 .col-lg-8{
            width:100%;
        }
        #fieldset_1_1 button.dropdown-toggle {
            display: inline-block !important;    
        }
    </style>
    {/if}
    <style>
        .cl-h2 {
            margin-top:1px;
            margin-bottom:-10px;
            text-align:center;
        }

        #lang_submit_faq {
            display: none;
        }
    </style>
    <script>
                var strFaq = "{l s='Are you sure want to delete faq record with id = ' mod='addcatdesc'}";
                var strBoxesFaq = "{l s='Are you sure want to delete faq records' mod='addcatdesc'}";
                var come_from = '{$name_controller|escape:'html':'UTF-8'}';
                var token = '{$token|escape:'htmlall':'UTF-8'}';
                var currentIndex = '{html_entity_decode($currentIndex|escape:'htmlall':'UTF-8')}';
                
                function delConfirmFaq(id){
                    if (confirm(strFaq+id+"?"))
                        location.href = this.getAttribute("href");
                }

                function delBoxesFaq(){
                    if ($(".descBoxFaq").is(':checked')) {
                        if (confirm(strBoxesFaq + "?")) {
                            $("#deleteboxfaqhidden").val(1);
                            $('#configuration_form').submit();    
                        }
                    }
                }

                function activeBoxesFaq(ch){
                    if ($(".descBoxFaq").is(':checked')) {
                        (ch === true ? $("#activeboxfaqhidden").val(1) : $("#activeboxfaqhidden").val(2));
                        $('#configuration_form').submit();
                    }
                }

                function checkBoxesFaq(ch) {
                    $('.descBoxFaq').prop('checked', ch);
                }
                function LangChangedFaq() {
                    $('#lang_submit_faq').click(); 
                }
    </script>
    <div class="row" style="width:100%;">

        {assign var=faq_tmp_list value=$input.values}
        {if (isset($faq_tmp_list) && !empty($faq_tmp_list))}

            {if isset($language_arr) && count($language_arr) > 1}
                <div class="lang_selection row">
                    <label for="lang_arr_sel_faq" class="col-lg-6">{l s='Select language for list:' mod='addcatdesc'}</label>
                    <select name="lang_arr_sel_faq" class="col-lg-6" onchange="LangChangedFaq();">
                        {foreach $language_arr as $key => $lang_el}
                            <option {if $lang_el['id_lang'] == $language_cur}selected{/if}  value="{$lang_el['id_lang']|intval}">{$lang_el['name']|escape:'htmlall':'UTF-8'}</option>
                        {/foreach}
                    </select>
                    <button type="submit" id="lang_submit_faq" name="langSubmitFaq"></button>
                </div>
            {/if}

            <div>
            <table cellspacing="0" cellpadding="0" style="width:100%;float:left;" class="table tableDnD cat" id="{if isset($id_text) && $id_text > 0}faq_text_list{else}faq_tmp_list{/if}">
                        <thead>
                            <tr class="nodrag nodrop">
                                <th width="1%">--</th>
                                <th width="2%">{l s='ID' mod='addcatdesc'}</th>
                                <th width="12%">{l s='Question' mod='addcatdesc'}</th>
                                <th width="65%">{l s='Answer' mod='addcatdesc'}</th>
                                <th width="5%" style="text-align:center;">{l s='Active' mod='addcatdesc'}</th>
                                <th width="5%" style="text-align:center;">{l s='Order' mod='addcatdesc'}</th>
                                <th width="10%" style="text-align:center;">{l s='Actions' mod='addcatdesc'}</th>
                            </tr>
                        </thead>
                        <tbody>
                           {foreach $faq_tmp_list as $key => $faq} 
                                <tr id="tr_{$faq['id_faq']|intval}_{if isset($faq['id_text']) && $faq['id_text'] > 0}{(int)$faq['id_text']|intval}{else}{(int)$faq['id_blck']|intval}{/if}_1"  class="panel">
                           <td><input type="checkbox" class="descBoxFaq" name="descBoxFaq[]" value="{$faq['id_faq']|intval}"/></td>
                                    <td>{$faq['id_faq']|intval}</td>
                                    <td>{$faq['question']|strip_tags|truncate:125:'...'|escape:'htmlall':'UTF-8'}</td>
                                    <td>{$faq['answer']|strip_tags|truncate:125:'...'|escape:'htmlall':'UTF-8'}</td>
                                    <td style="text-align:center;">
                                        {if $faq['active'] == 1}
        								<a class="btn btn-success" href="{$current|escape:'htmlall':'UTF-8'}&amp;token={$token|escape:'htmlall':'UTF-8'}&amp;changeStatusFaq&amp;id_faq={(int)$faq['id_faq']|intval}&amp;act=true&amp;{if isset($faq['id_text']) && $faq['id_text'] > 0}id_text={(int)$faq['id_text']|intval}{else}id_blck={(int)$faq['id_blck']|intval}{/if}">
        								    <i class="icon-check"></i>
        								</a>
        								{else}
        								<a class="btn btn-danger" href="{$current|escape:'htmlall':'UTF-8'}&amp;token={$token|escape:'htmlall':'UTF-8'}&amp;changeStatusFaq&amp;id_faq={(int)$faq['id_faq']|intval}&act=false&amp;{if isset($faq['id_text']) && $faq['id_text'] > 0}id_text={(int)$faq['id_text']|intval}{else}id_blck={(int)$faq['id_blck']|intval}{/if}">
        								    <i class="icon-remove"></i>
        								</a>
                                        {/if}
                                   </td>
                                   <td style="text-align:center;" class="pointer dragHandle center" id="td_{$faq['id_faq']|intval}_{if isset($faq['id_text']) && $faq['id_text'] > 0}{(int)$faq['id_text']|intval}{else}{(int)$faq['id_blck']|intval}{/if}">
                                        <div class="dragGroup">
                                            <div class="positions">
                                                {$faq['order']|intval}
                                            </div>
                                        </div>
                                    </td> 
                                    <td class="text-right">
                                        <div class="btn-group-action wrapper_action">
                                            <div class="btn-group pull-right">
                                    <a class="edit btn btn-default" href="{$current|escape:'htmlall':'UTF-8'}&amp;token={$token|escape:'htmlall':'UTF-8'}&amp;editFaq&amp;id_faq={(int)$faq['id_faq']|intval}&amp;{if isset($faq['id_text']) && $faq['id_text'] > 0}id_text={(int)$faq['id_text']|intval}{else}id_blck={(int)$faq['id_blck']|intval}{/if}" title="{l s='Edit' mod='addcatdesc'}">
                                 						<i class="icon-pencil"></i>
                              						    {l s='Edit' mod='addcatdesc'}
                                			         </a>
                                         			<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                    				<span class="caret"></span>
                                     			    </button>
                                         			<ul class="dropdown-menu menu_action" role="menu">
                                                        <li>
                                       					    <a href="{$current|escape:'htmlall':'UTF-8'}&amp;token={$token|escape:'htmlall':'UTF-8'}&amp;deleteFaq&amp;id_faq={(int)$faq['id_faq']|intval}&amp;{if isset($faq['id_text']) && $faq['id_text'] > 0}id_text={(int)$faq['id_text']|intval}{else}id_blck={(int)$faq['id_blck']|intval}{/if}" title="{l s='Delete' mod='addcatdesc'}" onclick="delConfirmFaq({$faq['id_faq']|intval}); return false;">
                                          						<i class="icon-trash"></i>
                                          						{l s='Delete' mod='addcatdesc'}
                                           					</a>
                                        				</li>
                                         			</ul>
                                            </div>
                		                  </div>
                                        </td>
                                </tr>
                            {/foreach}
                        </tbody>
			</table>
        </div>
        <div class="row">
                    <input type="hidden" id="deleteboxfaqhidden" name="deleteboxfaqhidden" value="0"/>
                    <input type="hidden" id="activeboxfaqhidden" name="activeboxfaqhidden" value="0"/>
                    
                            	<div class="col-lg-6">
                            		<div class="btn-group bulk-actions dropup">
                            			<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                            				{l s='Bulk actions' mod='addcatdesc'} <span class="caret"></span>
                            			</button>
                            			<ul class="dropdown-menu">
                            				<li>
                            					<a href="#" onclick="checkBoxesFaq(true);return false;">
                            						<i class="icon-check-sign"></i>&nbsp;{l s='Select all' mod='addcatdesc'}
                            					</a>
                            				</li>
                            				<li>
                            					<a href="#" onclick="checkBoxesFaq(false);return false;">
                            						<i class="icon-check-empty"></i>&nbsp;{l s='Unselect all' mod='addcatdesc'}
                            					</a>
                            				</li>
                            				<li class="divider"></li>
                                            <li>
												<a href="#" onclick="activeBoxesFaq(true);return false;">
							                         <i class="icon-power-off text-success"></i>&nbsp;{l s='Turn on selected' mod='addcatdesc'}
						                        </a>
											</li>
                                            <li>
												<a href="#" onclick="activeBoxesFaq(false);return false;">
							                         <i class="icon-power-off text-danger"></i>&nbsp;{l s='Turn off selected' mod='addcatdesc'}
						                        </a>
											</li>
                                            <li class="divider"></li>
                            					<li>
                            						<a href="#" onclick="delBoxesFaq();return false;">
                            							<i class="icon-trash"></i>&nbsp;{l s='Delete selected' mod='addcatdesc'}
                            						</a>
                            					</li>
                            			</ul>
                            		</div>
	                           </div>
            </div>
        {elseif isset($display) && $display == 'add'}            
            <h2 class="cl-h2">{l s='Please, choose FREE category and SAVE before adding FAQ.' mod='addcatdesc'}</h2>
        {elseif isset($display) && ($display == 'edit' || $display == 'editTmpBlock')}
            <h2 class="cl-h2">{l s='List of FAQ is empty. You can add new one.' mod='addcatdesc'}</h2>
        {else}
            <h2 class="cl-h2">{l s='Please, ENTER new title and SAVE block before adding FAQ.' mod='addcatdesc'}</h2>
        {/if}
    </div>
    {elseif $input.type == 'faq_blck_tmp_list'}
        {if (isset($new_version178) && $new_version178 > 0)}
        <style>
            #fieldset_2_2 .form-wrapper{
                max-width:100%;
            }
            #fieldset_2_2 button.dropdown-toggle {
                display: inline-block !important;
            }
        </style>
        {/if}

    <div class="row" style="width:100%;">
        {assign var=faq_blck_tmp_list value=$input.values}
        {if (isset($faq_blck_tmp_list) && count($faq_blck_tmp_list) > 0)}
            <script>
                var strBlck = "{l s='Are you sure want to delete template record with id = ' mod='addcatdesc'}";
                var strBoxesBlck = "{l s='Are you sure want to delete template records' mod='addcatdesc'}";
                
                function delConfirmBlck(id){
                    if (confirm(strBlck+id+"?"))
                        location.href = this.getAttribute("href");
                }

                function delBoxesBlck(){
                    if ($(".descBoxBlck").is(':checked')) {
                        if (confirm(strBoxesBlck + "?")) {
                            $("#deleteboxblckhidden").val(1);
                            $('#configuration_form').submit();    
                        }
                    }
                }

                function activeBoxesBlck(ch){
                    if ($(".descBoxBlck").is(':checked')) {
                        (ch === true ? $("#activeboxblckhidden").val(1) : $("#activeboxblckhidden").val(2));
                        $('#configuration_form').submit();
                    }
                }

                function checkBoxesBlck(ch) {
                    $('.descBoxBlck').prop('checked', ch);
                }

                function LangChangedTmp() {
                    $('#lang_submit_tmp').click(); 
                }
            </script>
            <style>
                #lang_submit_tmp {
                    display: none;
                }
            </style>

        {if isset($language_arr) && count($language_arr) > 1}
            <div class="lang_selection row">
                <label for="lang_arr_sel_tmp" class="col-lg-6">{l s='Select language for list:' mod='addcatdesc'}</label>
                <select name="lang_arr_sel_tmp" class="col-lg-6" onchange="LangChangedTmp();">
                    {foreach $language_arr as $key => $lang_el}
                        <option {if $lang_el['id_lang'] == $language_cur}selected{/if}  value="{$lang_el['id_lang']|intval}">{$lang_el['name']|escape:'htmlall':'UTF-8'}</option>
                    {/foreach}
                </select>
                <button type="submit" id="lang_submit_tmp" name="langSubmitTmp"></button>
            </div>
        {/if}

        <div>
            <table cellspacing="0" cellpadding="0" style="width:100%;float:left;" class="table tableDnD cat" id="faq_blck_tmp_list">
                        <thead>
                            <tr class="nodrag nodrop">
                                <th width="1%">--</th>
                                <th width="2%">{l s='ID' mod='addcatdesc'}</th>
                                <th width="12%">{l s='Title' mod='addcatdesc'}</th>
                                <th width="71%">{l s='FAQ' mod='addcatdesc'}</th>
                                <th width="5%" style="text-align:center;">{l s='Active' mod='addcatdesc'}</th>
                                <th width="10%" style="text-align:center;">{l s='Actions' mod='addcatdesc'}</th>
                            </tr>
                        </thead>
                        <tbody>
                           {foreach $faq_blck_tmp_list as $key => $blck} 
                                <tr id="tr_{$blck['id_blck']|intval}"  class="panel">
                                    <td><input type="checkbox" class="descBoxBlck" name="descBoxBlck[]" value="{$blck['id_blck']|intval}"/> </td>
                                    <td>{$blck['id_blck']|intval}</td>
                                    <td>{$blck['title']|escape:'htmlall':'UTF-8'}</td>
                                    <td>
                                        {if !empty($blck['faq'])}
                                            {foreach $blck['faq'] as $key => $faq}
    											<div>
                                                <strong>{$key+1|intval}</strong> - 
                                                    {$faq['question']|strip_tags|truncate:125:'...'|escape:'htmlall':'UTF-8'} - {$faq['answer']|strip_tags|truncate:125:'...'|escape:'htmlall':'UTF-8'}
    											</div>
    										{/foreach}
                                        {else}
										  {l s='Empty template block' mod='addcatdesc'}
                                        {/if}
										<div class="clear"></div>
									</td>
                                    <td style="text-align:center;">
                                        {if $blck['active'] == 1}
        								<a class="btn btn-success" href="{$current|escape:'htmlall':'UTF-8'}&amp;token={$token|escape:'htmlall':'UTF-8'}&amp;changeStatusBlock&amp;id_blck={(int)$blck['id_blck']|intval}&act=true">
        								    <i class="icon-check"></i>
        								</a>
        								{else}
        								<a class="btn btn-danger" href="{$current|escape:'htmlall':'UTF-8'}&amp;token={$token|escape:'htmlall':'UTF-8'}&amp;changeStatusBlock&amp;id_blck={(int)$blck['id_blck']|intval}&act=false">
        								    <i class="icon-remove"></i>
        								</a>
                                        {/if}
                                   </td>
                                    <td class="text-right">
                                        <div class="btn-group-action wrapper_action">
                                            <div class="btn-group pull-right">
                                			     <a class="edit btn btn-default" href="{$current|escape:'htmlall':'UTF-8'}&amp;token={$token|escape:'htmlall':'UTF-8'}&amp;editTmpBlock&amp;id_blck={(int)$blck['id_blck']|intval}" title="{l s='Edit' mod='addcatdesc'}">
                                 						<i class="icon-pencil"></i>
                              						    {l s='Edit' mod='addcatdesc'}
                                			         </a>
                                         			<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                    				<span class="caret"></span>
                                     			    </button>
                                         			<ul class="dropdown-menu menu_action" role="menu">
                                                        <li>
                                       					    <a href="{$current|escape:'htmlall':'UTF-8'}&amp;token={$token|escape:'htmlall':'UTF-8'}&amp;deleteTmpBlock&amp;id_blck={(int)$blck['id_blck']|intval}" title="{l s='Delete' mod='addcatdesc'}" onclick="delConfirmBlck({$blck['id_blck']|intval}); return false;">
                                          						<i class="icon-trash"></i>
                                          						{l s='Delete' mod='addcatdesc'}
                                           					</a>
                                        				</li>
                                         			</ul>
                                            </div>
                		                  </div>
                                        </td>
                                </tr>
                            {/foreach}
                        </tbody>
			</table>
        </div>
        <div class="row">
                    <input type="hidden" id="deleteboxblckhidden" name="deleteboxblckhidden" value="0"/>
                    <input type="hidden" id="activeboxblckhidden" name="activeboxblckhidden" value="0"/>
                    
                            	<div class="col-lg-6">
                            		<div class="btn-group bulk-actions dropup">
                            			<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                            				{l s='Bulk actions' mod='addcatdesc'} <span class="caret"></span>
                            			</button>
                            			<ul class="dropdown-menu">
                            				<li>
                            					<a href="#" onclick="checkBoxesBlck(true);return false;">
                            						<i class="icon-check-sign"></i>&nbsp;{l s='Select all' mod='addcatdesc'}
                            					</a>
                            				</li>
                            				<li>
                            					<a href="#" onclick="checkBoxesBlck(false);return false;">
                            						<i class="icon-check-empty"></i>&nbsp;{l s='Unselect all' mod='addcatdesc'}
                            					</a>
                            				</li>
                            				<li class="divider"></li>
                                            <li>
												<a href="#" onclick="activeBoxesBlck(true);return false;">
							                         <i class="icon-power-off text-success"></i>&nbsp;{l s='Turn on selected' mod='addcatdesc'}
						                        </a>
											</li>
                                            <li>
												<a href="#" onclick="activeBoxesBlck(false);return false;">
							                         <i class="icon-power-off text-danger"></i>&nbsp;{l s='Turn off selected' mod='addcatdesc'}
						                        </a>
											</li>
                                            <li class="divider"></li>
                            					<li>
                            						<a href="#" onclick="delBoxesBlck();return false;">
                            							<i class="icon-trash"></i>&nbsp;{l s='Delete selected' mod='addcatdesc'}
                            						</a>
                            					</li>
                            			</ul>
                            		</div>
	                           </div>
            </div>
        
        {else}
            <h2 style="margin-top:1px;margin-bottom:-10px;text-align:center;">{l s='You have not any templates for FAQ blocks. Add new one.' mod='addcatdesc'}</h2>
        {/if}
    </div>
    {elseif $input.type == 'tmp_name_sel'}
        <style> 
            #sel_template {
                height: 80px;
                line-height: 50px;
            }

            #sel_template option{
                width:100%;
            }

        </style>
        
        {assign var=tmp_name_sel value=$input.values}
        {if (isset($tmp_name_sel) && count($tmp_name_sel) > 0)}
        <select multiple class="form-control el-big" id="sel_template" name="sel_template[]" size="10">
            {foreach $tmp_name_sel as $key => $blck}
                {assign var=ch_exist value=0}

                {if isset($tmp_list_ch) && count($tmp_list_ch) > 0}
                    {foreach $tmp_list_ch as $key_c => $ch}
                        {if $blck['id_blck'] == $ch['id_blck']}
                            {$ch_exist = $blck['id_blck']}
                            {break}
                        {/if}
                    {/foreach}
                {/if}
                
                <option value="{$blck['id_blck']|intval}" {if $blck['id_blck'] == $ch_exist}selected="selected"{/if}>{$blck['title']|escape:'html':'UTF-8'}</option>
            {/foreach}
        </select>
        {else}
            {l s='You have not created any template' mod='addcatdesc'}
        {/if}
    {elseif $input.type == 'select_position'}
        <div class="col-lg-7" style="padding-left: 0px;">
   	        <select class="el-small" name="partial_position">
                <option value="0" {if $fields_value['partial_position'] == 0} selected="" {/if}>
                    {l s='Left' mod='addcatdesc'}
                </option>
                <option value="1" {if $fields_value['partial_position'] == 1} selected="" {/if}>
                    {l s='Center' mod='addcatdesc'}                                        
                </option>
                <option value="2" {if $fields_value['partial_position'] == 2} selected="" {/if}>
                    {l s='Right' mod='addcatdesc'}                                        
                </option>
            </select>
            <p class="help-block">
                {l s='This parameter works when Length for partial view > 0. Set button position for partial view.' mod='addcatdesc'}
            </p>
        </div>
	{elseif $input.type == 'select_category'}
    {if (isset($new_version178) && $new_version178 > 0)}
    <style>
    #fieldset_0 .form-wrapper {
        max-width: 90%;
    }
    #fieldset_0 label.col-lg-4 {
        width: 20%;
    }
    #fieldset_0 .col-lg-8 {
        width: 80%;
    }
    .form-group label {
        text-align:right !important;
    }
    </style>
    {/if}
	{assign var=category_sel value=$input.options.query}
	<select id="{$input.name|escape:'html':'UTF-8'}" name="{$input.name|escape:'html':'UTF-8'}" class="el-big">
            {foreach $category_sel as $item}
	            <option value="{$item['id_category']|intval}" {if isset($smarty.get.id_cat)}{if $smarty.get.id_cat==$item.id_category} selected="" {/if}{/if}>
    	               {$item['category_name']|escape:'html':'UTF-8'} {if isset($smarty.get.id_cat)}{if $smarty.get.id_cat==$item.id_category} {l s='(Editing...)' mod='addcatdesc'} {/if}{/if}
                </option>
            {/foreach}
 	</select>
    {elseif $input.type == 'shop_switch'}
        {if $shop_switch == true}
        <script>
            $(document).ready(function(){
                $(".form-group.shop_switch").parent('div.col-lg-8').css("width", "100%");
            });
        </script>
        <div class="form-group shop_switch">
            <label class="control-label col-lg-4">
                {l s='Save for all shops' mod='addcatdesc'}
            </label>
        <div class="col-lg-8">
		  <span class="switch prestashop-switch fixed-width-lg">
            <input name="save_all" id="loop_on" value="1" type="radio">
            <label for="loop_on">{l s='Yes' mod='addcatdesc'}</label>
            <input name="save_all" id="loop_off" value="0" checked="checked" type="radio">
            <label for="loop_off">{l s='No' mod='addcatdesc'}</label>
            <a class="slide-button btn"></a>
          </span>
          <p class="help-block">
            {l s='Save this additional category content for all active shops, except for categories which already exist' mod='addcatdesc'}
          </p>
        </div>
        </div>
        {/if}
    {elseif $input.type == 'shop_name_cur'}
        <div class="form-group">
            <div class="col-lg-8">
    		    <b style="font-size:18px;">{$shop_name_cur.name|escape:'html':'UTF-8'}</b>    
            </div>
        </div>
    {elseif $input.type == 'submit_text_button'}
        <style>
            #fieldset_0 {
                padding-bottom:0 !important;
            }
            #fieldset_0 .form-wrapper {
                width:100%;
                max-width:100%;
                padding-bottom:0;
            }
            
            #fieldset_0 .shop_switch .col-lg-8{
                width: 300px !important;
            }
        {if isset($display) && $display == 'edit'}
     	    #fieldset_0 .form-group:nth-last-of-type(3) .col-lg-8,
            #fieldset_0 .form-group:nth-last-of-type(3) .col-lg-9 {
                width:100%;
            }

          {else}
     	    #fieldset_0 .form-group:last-of-type .col-lg-8,
            #fieldset_0 .form-group:nth-last-of-type(1) .col-lg-9  {
                width:100%;
            }
            
          {/if}
        </style>
        {if isset($display) && ($display == 'editFaq' || $display == 'addFaq' || $display == 'editTmpBlock' || $display == 'addTmpBlock')}
        <style>
            #fieldset_0 label{
                text-align:right !important;
            }
        </style>
        {/if}
        {if isset($display) && $display == 'addFaq'}
            <style>
                #fieldset_0 .form-group:nth-last-of-type(5) .col-lg-8, #fieldset_0 .form-group:nth-last-of-type(5) .col-lg-9 {
                    width:100%;
                }
            </style>
        {elseif isset($display) && $display == 'editFaq'}    
            <style>
            #fieldset_0 .form-group:nth-last-of-type(6) .col-lg-8, #fieldset_0 .form-group:nth-last-of-type(6) .col-lg-9 {
                width:100%;
            }
            </style>
        {elseif isset($display) && $display == 'editTmpBlock'}    
            <style>
            #fieldset_0 .form-group:nth-last-of-type(2) .col-lg-8, #fieldset_0 .form-group:nth-last-of-type(2) .col-lg-9  {
                width:100%;
            }
            </style>
        {/if}
        
        <div class="panel-footer">
	       <a href="{$link->getAdminLink('AdminModules')|escape:'htmlall':'UTF-8'}&configure=addcatdesc{if isset($display) && ($display == 'editFaq' || $display == 'addFaq')}{if isset($id_blck) && $id_blck > 0}&editTmpBlock&id_blck={$id_blck|intval}{elseif isset($id_text) && $id_text > 0}&editCategoryText&id_cat={$id_cat|intval}&id_shop_cat={$id_shop_cat|intval}{/if}{/if}" class="btn btn-default"><i class="process-icon-cancel"></i> {l s='Cancel or Back to list' mod='addcatdesc'}</a>
            {if isset($display) && ($display == 'edit')}
                <a href="{$category_front_link}" class="btn btn-default" target="_blank" title="{l s='Opens in new window' mod='addcatdesc'}" ><i class="material-icons" >visibility</i> {l s='See results in FO' mod='addcatdesc'}</a>
            {/if}
	       <button id="form_submit_btn"  type="submit" name="{if isset($display) && $display == 'edit'}submitEditCategory{elseif isset($display) && $display == 'add'}submitSaveCategory{elseif isset($display) && $display == 'editTmpBlock'}submitEditFaqBlock{elseif isset($display) && $display == 'addTmpBlock'}submitAddFaqBlock{elseif isset($display) && $display == 'editFaq' && isset($id_blck) && $id_blck > 0}submitEditFaqTmp{elseif isset($display) && $display == 'addFaq' && isset($id_blck) && $id_blck > 0}submitAddFaqTmp{elseif isset($display) && $display == 'addFaq' && isset($id_text) && $id_text > 0}submitAddFaqCat{elseif isset($display) && $display == 'editFaq' && isset($id_text) && $id_text > 0}submitEditFaqCat{/if}" class="btn btn-default pull-right"><i class="process-icon-save"></i> {l s='Save' mod='addcatdesc'}</button>
           {if isset($display) && ($display == 'edit' || ($display == 'add' && isset($shop_num) && $shop_num == 1) || $display == 'editTmpBlock' || $display == 'addTmpBlock')}
                <button id="form_submit_stay_btn"  type="submit" name="{if $display == 'editTmpBlock'}submitEditFaqBlockStay{elseif $display == 'addTmpBlock'}submitAddFaqBlockStay{elseif $display == 'edit'}submitEditCategoryStay{elseif $display == 'add'}submitSaveCategoryStay{/if}" class="btn btn-default pull-right"><i class="process-icon-save"></i> {l s='Save and stay' mod='addcatdesc'}</button>
           {/if}
        </div>           
    {elseif $input.type == 'hidden_shop_name_ex'}
        <div></div>        
 	{elseif $input.type == 'button_activator'}
		{if $info_install == 0}
			<script type="text/javascript">
				var stract = "{l s='Are you sure?' mod='addcatdesc'}";
			    function actConfirm(){
			     
					if (confirm(stract)){
						$("#button_wrapper a").remove();
						$("#button_wrapper").prepend($('#addcatedesc_ajax_loader').html());
						$.ajax(
						{
							type: 'GET',
							url: '{$input.module_url}&action=activateAddCatDesc&ajax=true&secure_key={$input.secure_key|escape:"html":"UTF-8"}',
							dataType: 'html',
							cache: false, 
							success: function(result)
							{
								$('#button_wrapper').replaceWith(result);
							}
						});
					}
				}
			</script>
            {if $prestashop_version177 >= 0}
                <h2 style="color:#4eb357;">{l s='Do not use this tab if you see additional description of category in front-office.' mod='addcatdesc'}</h2>
            {/if}
	 		<h2>{l s='Manual installation:' mod='addcatdesc'}</h2>
	 		<div class="well" style="font-size:13px;" >
	 			{l s='This module works with templates/catalog/listing/product-list.tpl file of your theme. To display additional description of category in category page you need to copy and paste these text lines:' mod='addcatdesc'}
	 			<br/>
	 			<b>
				&#123;hook h='displayAddCatDesc' mod='addcatdesc'&#125;
				</b>
				<br/>
	 			{l s='into your templates/catalog/listing/product-list.tpl file (Your shop/themes/Your themetemplates/catalog/listing/product-list.tpl).' mod='addcatdesc'} {l s='Usually after the 66th line (different positions for different prestashop themes).' mod='addcatdesc'}
	 			<br/>
	 			{l s='Do not forget to clear cache.' mod='addcatdesc'}
	 		</div>
	 		<h2>{l s='Automatic installation:' mod='addcatdesc'}</h2>
	 		<div class="well" style="font-size:13px;" >
	 			{l s='You can change your templates/catalog/listing/product-list.tpl file automatically.  But it can fail depending on your theme. Clicking the activate button the system will try to make a backup copy of your file "templates/catalog/listing/product-list.tpl" of the active theme and will add the required code. The old file will be named "templates/catalog/listing/product-list-acd.tpl.bak" and will be located in your theme folder.' mod='addcatdesc'}
				<br/>
		 		<br />
		 		<div id="button_wrapper">
				 	<div id="addcatedesc_ajax_loader" style="display: none;">
				 		<p class="addcatedesc_ajax_loader"><img src="{$input.param|escape:'html':'UTF-8'}/views/img/loader.gif" alt="" /><br />{l s='Loading...' mod='addcatdesc'}</p>
			 		</div>
				 	<a href="#" onclick="actConfirm(); return false;" class="btn btn-lg btn-warning">{l s='ACTIVATE!' mod='addcatdesc'}<br />{l s='(I understand that, let\'s change templates/catalog/listing/product-list.tpl file automatically).' mod='addcatdesc'}</a>
		 		</div>
		 		<br />
		 		{l s='Do not forget to clear cache.' mod='addcatdesc'}
	 		</div>
		{else}
		<script type="text/javascript">
				var strdeact = "{l s='Are you sure?' mod='addcatdesc'}";
				
			    function deactConfirm(){
					if (confirm(strdeact)){
						$("#button_wrapper_deactivate a").remove();
						$("#button_wrapper_deactivate").prepend($('#addcatedesc_ajax_loader').html());
						$.ajax(
						{
							type: 'GET',
							url: '{$input.module_url}&action=deactivateAddCatDesc&ajax=true&secure_key={$input.secure_key|escape:"html":"UTF-8"}',
							dataType: 'html',
							cache: false, 
							success: function(result)
							{
								$('#button_wrapper_deactivate').replaceWith('<br/>' + result);
							}
						});
					}
				}
			</script>
			<div class="alert alert-success">{l s='You have necessary text lines in templates/catalog/listing/product-list.tpl file for this module!' mod='addcatdesc'}</div>
			<div class="alert alert-info">{l s='Before uninstall your module, please deactivate code from templates/catalog/listing/product-list.tpl file of your theme.' mod='addcatdesc'}</div>
			<h2>{l s='Manual deactivation:' mod='addcatdesc'}</h2>
			<div class="well" style="font-size:13px;" >
	 			{l s='You can deactivate module manually. Just find and delete these text lines in your templates/catalog/listing/product-list.tpl file of your theme:' mod='addcatdesc'}
				<br/>
				<b>
				&#123;hook h='displayAddCatDesc' mod='addcatdesc'&#125;
				</b>
				<br/>
	 			{l s='Do not forget to clear cache.' mod='addcatdesc'}
	 		</div>
	 		<h2>{l s='Automatic deactivation:' mod='addcatdesc'}</h2>
	 		<div class="well" style="font-size:13px;" >
	 			{l s='CAUTION: The templates/catalog/listing/product-list.tpl file will be restored from a backup, if you modified it all your changes will be lost.' mod='addcatdesc'}
				<br/>
	 		<div id="button_wrapper_deactivate">
			 	<div id="addcatedesc_ajax_loader" style="display: none;">
			 		<p class="addcatedesc_ajax_loader"><img src="{$input.param|escape:'html':'UTF-8'}/views/img/loader.gif" alt="" /><br />{l s='Loading...' mod='addcatdesc'}</p>
		 		</div>
		 		<br />
			 	<a href="#" onclick="deactConfirm(); return false;" class="btn btn-lg btn-warning">{l s='DEACTIVATE!' mod='addcatdesc'}<br />{l s='(I understand that, let\'s change templates/catalog/listing/product-list.tpl file automatically).' mod='addcatdesc'}</a>
			 	<br />
			 	<br />
			 	{l s='Do not forget to clear cache.' mod='addcatdesc'}
	 		</div>
	 		</div>
		{/if}
    {elseif $input.type == 'info_text'}
        <style type="text/css">
            #fieldset_3_3 .panel-heading-action, #fieldset_4_4 .panel-heading-action{
                display: none
            }           
            #fieldset_3_3 .col-lg-8, #fieldset_4_4 .col-lg-8, #fieldset_4_4 .col-lg-9 {
                width:100%;
            }
            #fieldset_3_3 .table tbody > tr > td, #fieldset_3_3 .table thead > tr > td, #fieldset_4_4 .table tbody > tr > td, #fieldset_3_3 .table thead > tr > td{
                font-size: 14px !important;
            }
            
            #fieldset_3_3 .form-wrapper, #fieldset_4_4 .form-wrapper {
                margin-left: 0px;
                margin-right: 0px;
                padding-right: 0px;
                padding-right: 0px;
                width: 100%;
                padding-left: 0px;
                max-width: 100%;
            }
            
        </style>
       <div>
            <table class="table tableDnD cat">
            <thead>
                <tr>
                    <td colspan="2">
                        <h4>{l s='You can use shortcodes (Turn it On in the settings for using) to display products in your content:' mod='addcatdesc'}</h4>
                    </td>
                </tr>
                </thead>
            <tbody>
                <tr>
                    <td><b>[#add_product:ID]</b></td>
                    <td><b>{l s='Display product with specified ID' mod='addcatdesc'}</b><br />
                        <b>ID</b> - {l s='Id of the product' mod='addcatdesc'} <br />
                        ({l s='For example - ' mod='addcatdesc'} <b>[#add_product:8]</b> - {l s='Display product with id = 8' mod='addcatdesc'})
                    </td>
                </tr>
                <tr>
                    <td><b>[#add_product_cat:ID-cat:NUM-products:PARAM:ORDER:VIEW]</b></td>
                    <td><b>{l s='Display products from specified category ID' mod='addcatdesc'}</b><br />
                    <b>ID-cat</b> - {l s='Id of the category you want to display products from.' mod='addcatdesc'} <br /> 
            <b>NUM-products</b> - {l s='Number of products for search' mod='addcatdesc'}  <br />
            <b>PARAM</b> - {l s='Parameter of search, it can be such words:' mod='addcatdesc'} <b>PRICE</b>, <b>DATE_ADD</b>, <b>DATE_UPD</b>, <b>ID_PRODUCT</b>, <b>SALES</b> <br /> 
            <b>ORDER</b> - {l s='Order of search, it can be such words:' mod='addcatdesc'} <b>ASC</b>, <b>DESC</b>  <br />
            <b>VIEW</b> - {l s='View of the block of products as the result. Use ' mod='addcatdesc'}<b>1</b> - {l s='to display products through comma, ' mod='addcatdesc'}<b>2</b> - {l s='to display products as the list.' mod='addcatdesc'} <br />
            ({l s='For example - ' mod='addcatdesc'} <b>[#add_product_cat:6:3:PRICE:DESC:1]</b> - {l s='Display 3 products through comma from category with id = 6 ordered by price desc' mod='addcatdesc'}) <br /> 
            ({l s='Another example - ' mod='addcatdesc'} <b>[#add_product_cat:6:4:DATE_ADD:ASC:2]</b> - {l s='Display 4 products as the list from category with id = 6 ordered by date_add asc' mod='addcatdesc'})</td>
                </tr>
                <tr>
                    <td><b>[#add_product_cat:0:NUM-products:PARAM:ORDER:VIEW]</b></td>
                    <td><b>{l s='Display products from category where visitor is. ID of the category will be taken automatically.' mod='addcatdesc'}</b><br />
            <b>NUM-products</b> - {l s='Number of products for search' mod='addcatdesc'}  <br />
            <b>PARAM</b> - {l s='Parameter of search, it can be such words:' mod='addcatdesc'} <b>PRICE</b>, <b>DATE_ADD</b>, <b>DATE_UPD</b>, <b>ID_PRODUCT</b>, <b>SALES</b> <br /> 
            <b>ORDER</b> - {l s='Order of search, it can be such words:' mod='addcatdesc'} <b>ASC</b>, <b>DESC</b>  <br />
            <b>VIEW</b> - {l s='View of the block of products as the result. Use ' mod='addcatdesc'}<b>1</b> - {l s='to display products through comma, ' mod='addcatdesc'}<b>2</b> - {l s='to display products as the list.' mod='addcatdesc'} <br />
            ({l s='For example - ' mod='addcatdesc'} <b>[#add_product_cat:0:3:PRICE:DESC:1]</b> - {l s='Display 3 products through comma from category ordered by price desc' mod='addcatdesc'}) <br /> 
            ({l s='Another example - ' mod='addcatdesc'} <b>[#add_product_cat:0:4:DATE_ADD:ASC:2]</b> - {l s='Display 4 products as the list from category  ordered by date_add asc' mod='addcatdesc'})</td>
                </tr>
                <tr>
                    <td colspan="2">
                        <h4>{l s='You can use shortcodes (Turn it On in the settings for using) to display category in your content:' mod='addcatdesc'}</h4>
                    </td>
                </tr>
                <tr>
                    <td><b>[#add_categoryname:ID]</b></td>
                    <td><b>{l s='Display category name with specified ID' mod='addcatdesc'}</b><br />
                        <b>ID</b> - {l s='Id of the category' mod='addcatdesc'} <br />
                        ({l s='For example - ' mod='addcatdesc'} <b>[#add_categoryname:3]</b> - {l s='Display category name with id = 3' mod='addcatdesc'})
                    </td>
                </tr>
                <tr>
                    <td><b>[#add_categoryname:0]</b></td>
                    <td><b>{l s='Display category name where visitor is. ID of the category will be taken automatically.' mod='addcatdesc'}</b><br />
                        {l s='For example - ' mod='addcatdesc'} <b>[#add_categoryname:0]</b> - {l s='Display category name where visitor is.' mod='addcatdesc'})
                    </td>
                </tr>
                <tr>
                    <td><b>[#add_categorylink:ID]</b></td>
                    <td><b>{l s='Display category link with specified ID. Not supported in FAQ title.' mod='addcatdesc'}</b><br />
                        <b>ID</b> - {l s='Id of the category' mod='addcatdesc'} <br />
                        ({l s='For example - ' mod='addcatdesc'} <b>[#add_categorylink:3]</b> - {l s='Display category link with id = 3' mod='addcatdesc'})
                    </td>
                </tr>
                <tr>
                    <td><b>[#add_categorylink:0]</b></td>
                    <td><b>{l s='Display category link where visitor is. ID of the category will be taken automatically. Not supported in FAQ title.' mod='addcatdesc'}</b><br />
                        {l s='For example - ' mod='addcatdesc'} <b>[#add_categorylink:0]</b> - {l s='Display category link where visitor is.' mod='addcatdesc'})
                    </td>
                </tr>
                </tbody>
            </table>
       </div>
	{else}
		{$smarty.block.parent}
    {/if}
{/block}