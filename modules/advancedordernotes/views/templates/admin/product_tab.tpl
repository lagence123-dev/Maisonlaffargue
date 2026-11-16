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
<div id="product-ordernotes" class="panel product-tab">
	<h3>{l s='Product comments' mod='advancedordernotes'}</h3>

	
	<div class="form-group">
		<label class="control-label col-lg-1" for="product_note">
			<span class="label-tooltip" >
				{l s='Note' mod='advancedordernotes'}
			</span>
		</label>
		<div class="col-lg-8">
			<textarea name="product_note" id="product_note" style="height: 100px;">{$product_note|escape:'htmlall':'UTF-8'}</textarea>
		</div>
	</div>
	
	<div class="panel-footer">
		<div class="col-lg-9">
			<button type="submit" rel="{$order_product_id|escape:'htmlall':'UTF-8'}" id="update_product_note" class="btn btn-default pull-right"><i class="process-icon-save"></i>{l s='Update note' mod='advancedordernotes'}</button>
		</div>
	</div>
</div>

<script type="text/javascript">
	
	var aon_token = "{$aon_token|escape:'htmlall':'UTF-8'}";
	
	jQuery(document).ready(function(){


		jQuery('body').on('click', '#update_product_note', function(){

			var r = confirm("Are you certain you want to update the note?");

			if (r == false) 
			{
			    return false;
			} 

			var el = jQuery(this);

			var response_z = $.ajax({ type: "POST",   
								url: '/modules/advancedordernotes/ajax_controller.php',  
								cache: false,
								data: { action: 'update_product_note',  token: aon_token, id_product: el.attr('rel'), note: jQuery('#product_note').val() }, 
								async: true,
								 success: function(data) 
								 {
								 	alert('Product updated.');

								}
							}).responseText;

			return false;


		});




	});

</script>