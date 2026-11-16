{*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License version 3.0
* that is bundled with this package in the file LICENSE.txt
* It is also available through the world-wide-web at this URL:
* https://opensource.org/licenses/AFL-3.0
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to a newer
* versions in the future. If you wish to customize this module for your
* needs please refer to CustomizationPolicy.txt file inside our module for more information.
*
* @author Webkul IN
* @copyright Since 2010 Webkul
* @license https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
*}

<div class='panel'>
   <div class="panel-heading">
      {if isset($edit)}
      <i class="icon-pencil"></i>
      {l s='Edit gift card' mod='wkgiftcard'}
      {else}
      <i class="icon-plus-circle"></i>
      {l s='Add gift card' mod='wkgiftcard'}
      {/if}
   </div>
   <div class="configurationTabs">
      <ul class="tab nav nav-tabs">
         <li class="tab-row active">
            <a class="tab-page" href="javascript:displayGiftCardModuleConfigTab('information');"
               id='gift_card_config_tab_link_information'>
            <i class="icon-info"></i>
            {l s='Information' mod='wkgiftcard'}
            </a>
         </li>
         <li class="tab-row">
            <a class="tab-page" href="javascript:displayGiftCardModuleConfigTab('condition');"
               id='gift_card_config_tab_link_condition'>
            <i class="icon-certificate"></i>
            {l s='Conditions' mod='wkgiftcard'}
            </a>
         </li>
      </ul>
   </div>
   <input type="hidden" id="current_config_tab" name="current_config_tab" value="information" />
   <form id="{$table|escape:'html':'UTF-8'}_form"
   class="defaultForm {$name_controller|escape:'html':'UTF-8'} form-horizontal"
   action="{$current|escape:'html':'UTF-8'}&token={$token|escape:'html':'UTF-8'}&{if isset($edit)}id={$idGiftCard|escape:'html':'UTF-8'}&update{$table|escape:'html':'UTF-8'}{else}add{$table|escape:'html':'UTF-8'}{/if}"
   method="post" enctype="multipart/form-data" {if isset($style)}style="{$style|escape:'html':'UTF-8'}" {/if}>
   <div id='gift_card_config_information' class="panel gift_card_config_tab">
      {* Name *}
      <div class="form-group">
         <label class="col-lg-3 control-label required" for="product_name">
         {l s='Name' mod='wkgiftcard'}
         </label>
         <div class="col-lg-7">
            <input type="hidden" value="{$idGiftCard|escape:'html':'UTF-8'}" name="idTable" id="idTable">
            {foreach from=$languages item=language}
            {assign var="gift_card_name" value="gift_card_name_`$language.id_lang`"}
            <input type="text" id="{$gift_card_name|escape:'html':'UTF-8'}"
            name="{$gift_card_name|escape:'html':'UTF-8'}"
            value="{if isset($giftCardProduct->name[$language.id_lang]) && $giftCardProduct->name[$language.id_lang]}{$giftCardProduct->name[$language.id_lang]|escape:'html':'UTF-8'}{else if isset($smarty.post.$gift_card_name)}{$smarty.post.$gift_card_name|escape:'html':'UTF-8'}{/if}"
            data-lang-name="{$language.name|escape:'html':'UTF-8'}"
            placeholder="{l s='Enter gift card name' mod='wkgiftcard'}"
            class="form-control gift_card_name_all"
            {if $currentLang.id_lang != $language.id_lang}style="display:none;" {/if} />
            {/foreach}
         </div>
         {if !empty($languages) && $languages|@count > 1}
         <div class="col-lg-2">
            <button type="button" class="wkgiftcard_lang_btn btn btn-default dropdown-toggle"
               data-toggle="dropdown">
            {$currentLang.iso_code|escape:'html':'UTF-8'}
            <span class="caret"></span>
            </button>
            <ul class="dropdown-menu">
               {foreach from=$languages item=language}
               <li>
                  <a href="javascript:void(0)"
                     onclick="showGiftCardLangField('{$language.iso_code|escape:'html':'UTF-8'}', {$language.id_lang|escape:'html':'UTF-8'});">{$language.name|escape:'html':'UTF-8'}</a>
               </li>
               {/foreach}
            </ul>
         </div>
         {/if}
      </div>
      {* Description *}
      <div class="form-group">
         <label for="short_description" class="col-lg-3 control-label">
         {l s='Short description' mod='wkgiftcard'}
         </label>
         <div class="col-lg-7">
            {foreach from=$languages item=language}
            {assign var="description_short" value="description_short`$language.id_lang`"}
            <div id="short_desc_div_{$language.id_lang|escape:'html':'UTF-8'}"
            class="wk_text_field_all wk_text_field_{$language.id_lang|escape:'html':'UTF-8'}"
            {if $currentLang.id_lang != $language.id_lang}style="display:none;" {/if}>
            <textarea name="description_short{$language.id_lang|escape:'html':'UTF-8'}"
               id="short_description_{$language.id_lang|escape:'html':'UTF-8'}" cols="2" rows="3"
               class="wk_tinymce form-control gift_card_short_desc">{if isset($smarty.post.$description_short)}{$smarty.post.$description_short|escape:'html':'UTF-8'}{elseif isset($edit)}{$giftCardProduct->description_short[{$language.id_lang|escape:'html':'UTF-8'}]|escape:'html':'UTF-8'}{/if}</textarea>
         </div>
         {/foreach}
      </div>
      {if !empty($languages) && $languages|@count > 1}
      <div class="col-lg-2">
         <button type="button" class="wkgiftcard_lang_btn btn btn-default dropdown-toggle"
            data-toggle="dropdown">
         {$currentLang.iso_code|escape:'html':'UTF-8'}
         <span class="caret"></span>
         </button>
         <ul class="dropdown-menu">
            {foreach from=$languages item=language}
            <li>
               <a href="javascript:void(0)"
                  onclick="showGiftCardLangField('{$language.iso_code|escape:'html':'UTF-8'}', {$language.id_lang|escape:'html':'UTF-8'});">{$language.name|escape:'html':'UTF-8'}</a>
            </li>
            {/foreach}
         </ul>
      </div>
      {/if}
   </div>
   {* Tags *}
   <div class="form-group">
      <label class="col-lg-3 control-label" for="product_name">
      {l s='Tags' mod='wkgiftcard'}
      </label>
      <div class="col-lg-7">
         {foreach from=$languages item=language}
         <div class="wk_text_field_all wk_text_field_{$language.id_lang|escape:'html':'UTF-8'}"
         {if $currentLang.id_lang != $language.id_lang}style="display:none;" {/if}>
         {literal}
         <script type="text/javascript">
            $(document).ready(function() {
            var input_id = '{/literal}wkgift_tags_{$language.id_lang|escape:'html':'UTF-8'}{literal}';
            $('#'+input_id).tagify({delimiters: [13,44], addTagPrompt: '{/literal}{l s='Add tag' js=1}{literal}'});
                $({/literal}'#{$table|escape:'html':'UTF-8'}{literal}_form').submit( function() {
                $(this).find('#' + input_id).val($('#' + input_id).tagify('serialize'));
                });
                });
         </script>
         {/literal}
         {assign var="wkgift_tags" value="wkgift_tags_`$language.id_lang`"}
         <input type="text" name="wkgift_tags_{$language.id_lang|escape:'html':'UTF-8'}"
            id="wkgift_tags_{$language.id_lang|escape:'html':'UTF-8'}"
            value="{if isset($giftCardProduct->tags[$language.id_lang])}{$giftCardProduct->getTags($language.id_lang)|escape:'html':'UTF-8'}{/if}"
            class='form-control tagify updateCurrentText'>
      </div>
      {/foreach}
      <p class="help-block">
         {l s='Use a comma to create separate tags.' mod='wkgiftcard'}
      </p>
   </div>
   {if !empty($languages) && $languages|@count > 1}
   <div class="col-lg-2">
      <button type="button" class="wkgiftcard_lang_btn btn btn-default dropdown-toggle"
         data-toggle="dropdown">
      {$currentLang.iso_code|escape:'html':'UTF-8'}
      <span class="caret"></span>
      </button>
      <ul class="dropdown-menu">
         {foreach from=$languages item=language}
         <li>
            <a href="javascript:void(0)"
               onclick="showGiftCardLangField('{$language.iso_code|escape:'html':'UTF-8'}', {$language.id_lang|escape:'html':'UTF-8'});">{$language.name|escape:'html':'UTF-8'}
            </a>
         </li>
         {/foreach}
      </ul>
   </div>
   {/if}
</div>
{* Image *}
<div class="form-group">
   <label class="col-lg-3 control-label">
   {l s='Image' mod='wkgiftcard'}
   </label>
   <div class="col-lg-8">
      <div class="form-group">
         <div class="col-sm-6">
            <input id="gift_image" type="file" name="gift_image" class="hide">
            <div class="dummyfile input-group">
               <span class="input-group-addon"><i class="icon-file"></i></span>
               <input id="gift_image-name" type="text" name="gift_image" readonly="">
               <span class="input-group-btn">
               <button id="gift_image-selectbutton" type="button" name="submitAddAttachments"
                  class="btn btn-default">
               <i class="icon-folder-open"></i>
               {l s='Add image' mod='wkgiftcard'}
               </button>
               </span>
            </div>
         </div>
      </div>
      <p class="help-block">
         {l s='Only jpg, png, jpeg, webp and gif extension are allowed.' mod='wkgiftcard'}
      </p>
      <p class="help-block">
         {l s='Recommended size 800 x 800px for default theme and ' mod='wkgiftcard'}
         {$imageSize|escape:'html':'UTF-8'}
      </p>
   </div>
</div>
{* Image Preview *}
{if isset($gift_image_src)}
<div class="form-group">
   <label class="col-sm-3 control-label"></label>
   <div class="col-sm-8">
      <div class="form-group">
         <div>
            <img class="img-thumbnail" src="{$gift_image_src|escape:'html':'UTF-8'}" />
         </div>
      </div>
   </div>
</div>
{/if}
{* Dynamic Price *}
<div class="form-group">
   <label class="col-lg-3 control-label">
   {l s='Dynamic price' mod='wkgiftcard'}
   </label>
   <div class="col-lg-6">
      <span class="switch prestashop-switch fixed-width-lg">
      <input onclick="showprice(false)" type="radio" value="1" id="dynamic_price_on"
      name="dynamic_price" {if isset($edit)}
      {if $giftCardObj->dynamic_price==1} checked="checked"
      {/if}
      {/if}>
      <label for="dynamic_price_on">{l s='Yes' mod='wkgiftcard'}</label>
      <input onclick="showprice(true)" type="radio" value="0" id="dynamic_price_off"
      name="dynamic_price" {if isset($edit)}
      {if $giftCardObj->dynamic_price==0} checked="checked"
      {/if}
      {else} checked="checked"
      {/if}>
      <label for="dynamic_price_off">{l s='No' mod='wkgiftcard'}</label>
      <a class="slide-button btn"></a>
      </span>
      <p class="help-block">{l s='If yes, customer can enter gift card price.' mod='wkgiftcard'}</p>
   </div>
</div>
{* Quantity *}
<div class="form-group">
   <label class="col-lg-3 control-label required" for="gift_card_quantity">
   {l s='Quantity ' mod='wkgiftcard'}
   </label>
   <div class="col-lg-4">
      <input type="text" placeholder="{l s='Enter gift card quantity' mod='wkgiftcard'}"
         class="form-control" id="gift_card_quantity" name="gift_card_quantity"
         value="{if isset($smarty.post.gift_card_quantity) && $smarty.post.gift_card_quantity}{$smarty.post.gift_card_quantity|escape:'html':'UTF-8'}{elseif isset($edit) && isset($product_quanity)}{$product_quanity|escape:'html':'UTF-8'}{/if}" />
   </div>
</div>
{* Price *}
<div class="form-group" id="price_div">
   <label class="col-lg-3 control-label required" for="product_price">
   {l s='Price' mod='wkgiftcard'}
   </label>
   <div class="col-lg-4">
      <div class="input-group">
         <div class="input-group-addon">
            {$adminCurrency->sign|escape:'html':'UTF-8'}
         </div>
         <input type="text" placeholder="{l s='Enter gift card price' mod='wkgiftcard'}"
            class="form-control" id="product_price" name="price"
            value="{if isset($smarty.post.price) && $smarty.post.price}{$smarty.post.price|escape:'html':'UTF-8'}{elseif isset($edit) && isset($giftCardObj->amount)}{$giftCardObj->amount|escape:'html':'UTF-8'}{/if}" />
      </div>
   </div>
</div>
{* Valid Upto *}
<div class="form-group">
   <label class="col-lg-3 control-label required">
   {l s='Valid upto' mod='wkgiftcard'}
   </label>
   <div class="col-lg-4">
      <div class="input-group">
         <input type="text" class="form-control"
            placeholder="{l s='Enter gift card valid upto in month' mod='wkgiftcard'}" name="valid_upto"
            value="{if isset($smarty.post.valid_upto) && $smarty.post.valid_upto}{$smarty.post.valid_upto|escape:'html':'UTF-8'}{elseif isset($edit) && isset($giftCardObj->valid_upto)}{$giftCardObj->valid_upto|escape:'html':'UTF-8'}{/if}" />
         <div class="input-group-addon">
            <span>
            {l s='Month(s)' mod='wkgiftcard'}
            </span>
         </div>
      </div>
   </div>
</div>
{* Category *}
<div class="form-group">
   <label class="col-lg-3 control-label required" for="product_category">
   {l s='Category' mod='wkgiftcard'}
   </label>
   <div class="col-lg-6">
      <div id="categorycontainer"></div>
      <input type="hidden" name="product_category" id="product_category"
         value="{if isset($catIdsJoin)}{$catIdsJoin|escape:'htmlall':'UTF-8'}{/if}" />
   </div>
</div>
{* Default Product Category *}
<div class="form-group" id="default_category_div">
   <label class="col-lg-3 control-label required" for="default_category">
   {l s='Default product category ' mod='wkgiftcard'}
   </label>
   <div class="col-lg-6">
      <select name="default_category" class="fixed-width-xl" id="default_category">
         {if isset($defaultCategory)}
         {foreach $defaultCategory as $category}
         <option id="default_cat{$category.id_category|escape:'htmlall':'UTF-8'}"
         value="{$category.id_category|escape:'htmlall':'UTF-8'}" {if isset($defaultIdCategory)}
         {if $defaultIdCategory == $category.id_category} selected {/if} {/if}>
         {$category.name|escape:'htmlall':'UTF-8'}
         </option>
         {/foreach}
         {else}
         <option id="default_cat2" value="2">{l s='Home' mod='wkgiftcard'}</option>
         {/if}
      </select>
   </div>
</div>

{* Reference *}
<div class="form-group">
   <label class='col-lg-3 control-label' for='gift_card_reference'>
   <span class="label-tooltip" data-toggle="tooltip" data-html="true"
      title="{l s='Allowed special characters: .-_#' mod='wkgiftcard'}">
   {l s='Reference' mod='wkgiftcard'}
   </span>
   </label>
   <div class="col-lg-4">
      <input type='text' class="form-control" id='gift_card_reference' name='gift_card_reference'
         value="{if isset($smarty.post.gift_card_reference) && $smarty.post.gift_card_reference}{$smarty.post.gift_card_reference|escape:'html':'UTF-8'}{elseif isset($edit) && isset($giftCardProduct->reference)}{$giftCardProduct->reference|escape:'html':'UTF-8'}{/if}" />
   </div>
</div>
{* UPC Barcode *}
<div class="form-group">
   <label class='col-lg-3 control-label' for='gift_card_quantity'>
   {l s='UPC barcode ' mod='wkgiftcard'}
   </label>
   <div class="col-lg-4">
      <input type='text' class="form-control" id='gift_card_upc_barcode' name='gift_card_upc_barcode'
         value="{if isset($smarty.post.gift_card_upc_barcode) && $smarty.post.gift_card_upc_barcode}{$smarty.post.gift_card_upc_barcode|escape:'html':'UTF-8'}{elseif isset($edit) && isset($giftCardProduct->upc)}{$giftCardProduct->upc|escape:'html':'UTF-8'}{/if}" />
   </div>
</div>
{* EAN 13 *}
<div class="form-group">
   <label class='col-lg-3 control-label' for='gift_card_quantity'>
   {l s='EAN-13 or JAN barcode' mod='wkgiftcard'}
   </label>
   <div class="col-lg-4">
      <input type='text' class="form-control" id='gift_card_ean_13' name='gift_card_ean_13'
         value="{if isset($smarty.post.gift_card_ean_13) && $smarty.post.gift_card_ean_13}{$smarty.post.gift_card_ean_13|escape:'html':'UTF-8'}{elseif isset($edit) && isset($giftCardProduct->ean13)}{$giftCardProduct->ean13|escape:'html':'UTF-8'}{/if}" />
   </div>
</div>
{* Enable *}
<div class="form-group">
   <input type="hidden" id="id_ps_product" {if isset($edit) && isset($giftCardProduct->id)}
   value="{$giftCardProduct->id|escape:'html':'UTF-8'}" {/if}>
   <label class="col-lg-3 control-label">{l s='Enable' mod='wkgiftcard'}</label>
   <div class="col-lg-6">
      <span class="switch prestashop-switch fixed-width-lg">
      <input type="radio" value="1" id="product_active_on" name="active" {if isset($edit)}
      {if $giftCardProduct->active==1} checked="checked" {/if} {else}checked="checked" {/if}>
      <label for="product_active_on">{l s='Yes' mod='wkgiftcard'}</label>
      <input type="radio" value="0" id="product_active_off" name="active"
      {if isset($edit) && $giftCardProduct->active==0} checked="checked" {/if}>
      <label for="product_active_off">{l s='No' mod='wkgiftcard'}</label>
      <a class="slide-button btn"></a>
      </span>
   </div>
</div>
</div>
{* Conditions *}
<div id='gift_card_config_condition' class="panel gift_card_config_tab">
   <form id="{$table|escape:'html':'UTF-8'}_form"
   class="defaultForm {$name_controller|escape:'html':'UTF-8'} form-horizontal"
   action="{$current|escape:'html':'UTF-8'}&token={$token|escape:'html':'UTF-8'}&{if isset($edit)}id={$idGiftCard|escape:'html':'UTF-8'}&update{$table|escape:'html':'UTF-8'}{else}add{$table|escape:'html':'UTF-8'}{/if}"
   method="post" enctype="multipart/form-data" {if isset($style)}style="{$style|escape:'html':'UTF-8'}"
   {/if}>
   {* Tax Rate *}
   <div class="form-group ">
      <label class="col-lg-3 control-label">
      {l s='Tax rate' mod='wkgiftcard'}
      </label>
      <div class="col-lg-3">
         <select name="id_tax_rules_group" id="id_tax_rules_group"
            class="form-control form-control-select" data-action="input_excl">
            <option value="0">{l s='No tax' mod='wkgiftcard'}</option>
            {foreach $tax_rules_groups as $tax_rule}
            <option value="{$tax_rule.id_tax_rules_group|escape:'html':'UTF-8'}"
            {if isset($giftCardProduct->id_tax_rules_group)}
            {if $giftCardProduct->id_tax_rules_group == $tax_rule.id_tax_rules_group}
            selected="selected" {/if}
            {/if}>
            {$tax_rule.name|escape:'html':'UTF-8'}
            </option>
            {/foreach}
         </select>
      </div>
   </div>
   <div class="form-group">
      <label class="col-lg-3 control-label">{l s='Free shipping' mod='wkgiftcard'}</label>
      <div class="col-lg-6">
         <span class="switch prestashop-switch fixed-width-lg">
         <input type="radio" value="1" id="free_shipping_on" name="free_shipping"
         {if isset($edit) && $giftCardObj->free_shipping==1} checked="checked" {/if}>
         <label for="free_shipping_on">{l s='Yes' mod='wkgiftcard'}</label>
         <input type="radio" value="0" id="free_shipping_off" name="free_shipping" {if isset($edit)}
         {if $giftCardObj->free_shipping==0} checked="checked" {/if} {else}checked="checked"
         {/if}>
         <label for="free_shipping_off">{l s='No' mod='wkgiftcard'}</label>
         <a class="slide-button btn"></a>
         </span>
         <p class="help-block">
            {l s='If yes, customers will get shipping price as discount (gift card price + shipping price => discount price).' mod='wkgiftcard'}
         </p>
      </div>
   </div>
   <div class="form-group">
      <label class="col-lg-3 control-label">{l s='Voucher for every quantity' mod='wkgiftcard'}</label>
      <div class="col-lg-6">
         <span class="switch prestashop-switch fixed-width-lg">
         <input type="radio" value="1" id="every_quantity_on" name="every_quantity" {if isset($edit)}
         {if $giftCardObj->together==1} checked="checked" {/if} {else}checked="checked" {/if}>
         <label for="every_quantity_on">{l s='Yes' mod='wkgiftcard'}</label>
         <input type="radio" value="0" id="every_quantity_off" name="every_quantity"
         {if isset($edit) && $giftCardObj->together==0} checked="checked" {/if}>
         <label for="every_quantity_off">{l s='No' mod='wkgiftcard'}</label>
         <a class="slide-button btn"></a>
         </span>
         <p class="help-block">
            {l s='If no, a single voucher will get created with the purchased quantity(s).' mod='wkgiftcard'}
         </p>
      </div>
   </div>
   <div class="form-group">
      <label class="col-lg-3 control-label">{l s='Select product' mod='wkgiftcard'}</label>
      <div class="col-lg-6">
         <span class="switch prestashop-switch fixed-width-lg">
         <input onclick="showproduct(true)" type="radio" value="1" id="is_select_product_on"
         name="is_select_product" {if isset($edit)}
         {if $giftCardObj->choose_product==1}
         checked="checked" {/if}
         {/if}>
         <label for="is_select_product_on">{l s='Yes' mod='wkgiftcard'}</label>
         <input onclick="showproduct(false)" type="radio" value="0" id="is_select_product_off"
         name="is_select_product" {if isset($edit)}
         {if $giftCardObj->choose_product==0}
         checked="checked" {/if}
         {else} checked="checked"
         {/if}>
         <label for="is_select_product_off">{l s='No' mod='wkgiftcard'}</label>
         <a class="slide-button btn"></a>
         </span>
         <p class="help-block">
            {l s='If no, gift card voucher will apply on all product.' mod='wkgiftcard'}
         </p>
      </div>
   </div>
   <div id="id_choose_product">
      <div class="form-group">
         <label class="col-lg-3 control-label">{l s='Product' mod='wkgiftcard'}</label>
         <div class="col-lg-9">
            <div class="row">
               <div class="col-lg-9">
                  <input type="text" class="form-control" id="gift_product" name="gift_product" />
                  <ul id="wk_giftProd_suggestion_cont"></ul>
               </div>
               <div class="col-lg-3" style="40px;">
                  <button class="btn btn-outline-secondary" id="add_select_item" type="button">
                  {l s='Add this product' mod='wkgiftcard'}
                  </button>
               </div>
            </div>
            <p class="help-block">
               {l s='Search products by name or reference to apply the gift card voucher on them.' mod='wkgiftcard'}
            </p>
         </div>
      </div>
      <div class="form-group listOfPack">
         <div class="col-md-12">
            <label class="control-label product_description">
            {l s='List of selected product' mod='wkgiftcard'}
            </label>
         </div>
         <div class="col-md-12">
            <p class="alert alert-warning pack-empty-warning" {if isset($pack_items)}
            {if $pack_items|@count != 0}style="display:none" {/if}{/if}>
            {l s='No product selected' mod='wkgiftcard'}</p>
            <div class="row" id="divItems">
               {if isset($pack_items)}
               {foreach $pack_items as $pack_item}
               <div class="col-md-3 mb-3">
                  <div class="wk-gift-prod-cont">
                     <img src="{$pack_item.image|escape:'html':'UTF-8'}">
                     <h4>{$pack_item.name|escape:'html':'UTF-8'}</h4>
                     <span>{$pack_item.ref|escape:'html':'UTF-8'}</span>
                     <button class="btn btn-default deleteGiftProd" type="button"><i
                        class="material-icons">delete</i></button>
                     <input type="hidden" class="pspk_id_prod" name="pspk_id_prod[]"
                        value="{$pack_item.id|escape:'html':'UTF-8'}">
                  </div>
               </div>
               {/foreach}
               {/if}
            </div>
         </div>
         <div class="col-lg-12 wk-inline-block">
            <div id="form_hooks_gift_product"></div>
         </div>
      </div>
   </div>

   {* Select Category  *}
   <div class="form-group">
      <label class="col-lg-3 control-label">{l s='Select category' mod='wkgiftcard'}</label>
      <div class="col-lg-6">
         <span class="switch prestashop-switch fixed-width-lg">
         <input onclick="showCategory(true)" type="radio" value="1" id="is_select_category_on"
         name="is_select_category" {if isset($edit)}
         {if $giftCardObj->is_category_restriction==1}
         checked="checked" {/if}
         {/if}>
         <label for="is_select_category_on">{l s='Yes' mod='wkgiftcard'}</label>
         <input onclick="showCategory(false)" type="radio" value="0" id="is_select_category_off"
         name="is_select_category" {if isset($edit)}
         {if $giftCardObj->is_category_restriction==0}
         checked="checked" {/if}
         {else} checked="checked"
         {/if}>
         <label for="is_select_category_off">{l s='No' mod='wkgiftcard'}</label>
         <a class="slide-button btn"></a>
         </span>
         <p class="help-block">
            {l s='If no, gift card voucher will apply on all categories.' mod='wkgiftcard'}
         </p>
      </div>
   </div>
   <div id='id_choose_category'>
      <div class="form-group">
         <label class="col-lg-3 control-label" for="product_category_condition">
            {l s='Category' mod='wkgiftcard'}
         </label>
         <div class="col-lg-6">
         <div id="categorycontainerconditoin"></div>
            <input type='hidden' name='product_category_condition' id="product_category_condition"  value="{if isset($catConIdsJoin)}{$catConIdsJoin|escape:'htmlall':'UTF-8'}{/if}"/>
         </div>
      </div>
   </div>
</div>
<div class="panel-footer">
   <a href="{$link->getAdminLink('AdminGiftCard')|escape:'html':'UTF-8'}" class="btn btn-default">
   <i class="process-icon-cancel"></i>{l s='Cancel' mod='wkgiftcard'}
   </a>
   <button type="submit" name="submitAdd{$table|escape:'html':'UTF-8'}"
      class="btn btn-default pull-right submitGiftCardProduct">
   <i class="process-icon-save"></i>{l s='Save' mod='wkgiftcard'}
   </button>
   <button type="submit" name="submitAdd{$table|escape:'html':'UTF-8'}AndStay"
      class="btn btn-default pull-right submitGiftCardProduct">
   <i class="process-icon-save"></i> {l s='Save and stay' mod='wkgiftcard'}
   </button>
</div>
</div>
</form>