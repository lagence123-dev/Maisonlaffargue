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
*  @copyright  2007-2017 PrestaShop SA
*  @version  Release: $Revision: 14011 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<link href="{$aon_url|escape:'htmlall':'UTF-8'}/modules/advancedordernotes/views/css/adminadvancedordernotes.css" rel="stylesheet" type="text/css" media="all" />
<script type="text/javascript">
	var aon_token_stickies = "{$aon_token|escape:'htmlall':'UTF-8'}";
	var add_note = "{l s='Add sticky note'  mod='advancedordernotes'}";
	var aon_url = "{$aon_url|escape:'htmlall':'UTF-8'}";
	var aon_sticky_notes = '<div class="row">';

	{foreach from=$aon_sticky_notes item=sn}
		aon_sticky_notes += '<div class="col-lg-2 col-s-6 col-md-2 cold-xs-6 aon_sticky_note"><div class="panel toolbarBox " style="padding:10px;" ><img rel="{$sn.id|escape:'htmlall':'UTF-8'}" class="aon_delete_sticky" src="{$aon_url|escape:'htmlall':'UTF-8'}/modules/advancedordernotes/views/img/sticky_trash.png" /> {$sn.note|escape:'htmlall':'UTF-8'|replace:"\n":""}<span class="aon_sticky_owner">{$sn.employee_name|escape:'htmlall':'UTF-8'} <span class="aon_sticky_time">{$sn.date_add|escape:'htmlall':'UTF-8'}</span> </span></div></div>';
	{/foreach}

	aon_sticky_notes += '</div>';

	var sticky_error = "{l s=' Sticky must have at least 3 characters'  mod='advancedordernotes'}";
	var order_note_created = "{l s='Your sticky note has been created !'  mod='advancedordernotes'}";
	var aon_sticky_date = "<select id='aon_sticky_date'><option value='1'>{l s='Expire after'  mod='advancedordernotes'} 1 {l s='day'  mod='advancedordernotes'}</option><option value='2'>{l s='Expire after'  mod='advancedordernotes'} 2 {l s='days'  mod='advancedordernotes'}</option><option value='3'>{l s='Expire after'  mod='advancedordernotes'} 3 {l s='days'  mod='advancedordernotes'}</option><option value='4'>{l s='Expire after'  mod='advancedordernotes'} 4 {l s='days'  mod='advancedordernotes'}</option><option value='5'>{l s='Expire after'  mod='advancedordernotes'} 5 {l s='days'  mod='advancedordernotes'}</option><option value='6'>{l s='Expire after'  mod='advancedordernotes'} 6 {l s='days'  mod='advancedordernotes'}</option><option value='7' selected>{l s='Expire after'  mod='advancedordernotes'} 7 {l s='day'  mod='advancedordernotes'}</option><option value='14'>{l s='Expire after'  mod='advancedordernotes'} 14 {l s='days'  mod='advancedordernotes'}</option><option value='30'>{l s='Expire after'  mod='advancedordernotes'} 30 {l s='days'  mod='advancedordernotes'}</option></select>";
	var admin_order_notes = "{$aon_url|escape:'htmlall':'UTF-8'}/modules/advancedordernotes/ajax_controller.php";
	var aon_employees = [];
	{foreach from=$aon_employees item=emp}
	 	var e = {};
	 	e.id = {$emp.id_employee|escape:'htmlall':'UTF-8'};
	 	e.name = "{l s='Show to: '  mod='advancedordernotes'} {$emp.firstname|escape:'htmlall':'UTF-8'} {$emp.lastname|escape:'htmlall':'UTF-8'}" ;
	 	aon_employees.push(e);
	{/foreach}
</script>
<script type="text/javascript" src="{$aon_url|escape:'htmlall':'UTF-8'}/modules/advancedordernotes/views/js/sticky.js" ></script>
{if $is_15 == 1}
	<style type="text/css">
		#ordernotes_comment { width: 100% }
		.aon_sticky_note .panel {    margin: 0px 5px 10px 0px;  }
		@media screen and (max-width: 480px) {
		    .aon_sticky_note {
		    	width:50%;
		    }
		}
		@media screen and (min-width: 481px) {
		    .aon_sticky_note {
		    	width:16.666%;
		    	display: inline-block;
		    }
		}


	</style>
{/if}
