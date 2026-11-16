/*
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
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/


$(document).ready(function(){
	

	if(aon_position == 'top')
	{	
		if($('#content').length >= 1)
			$('.advancedordernotes_wrapper').detach().prependTo('#content');
		
		if($('#order-view-page').length >= 1)
			$('.advancedordernotes_wrapper').detach().prependTo('#order-view-page');
	}


	$('body').on('click', '#addOrderNote',function(){

			if($('#ordernotes_comment').val().length < 3){
				alert('Note cannot be empty');
				return false;
			}

			var response_z = $.ajax({ type: "POST",   
					url: '/modules/advancedordernotes/ajax_controller.php',  
					cache: false,
				 	data: {  action: 'add_order_note', note: $('#ordernotes_comment').val(), hidden_id_order: $('#hidden_id_order').val(), hidden_id_employee: $('#hidden_id_employee').val(), note_status: $('#note_statuses').val(), token: $('#aon_token').val() }, 
					async: true,
					success: function(data) 
					{
						$('#order_notes').append(data);
						$('#ordernotes_comment').val('');
						$('.noorders').remove();
					}
					
					}).responseText;

		return false;
	});


	$('body').on('click', '.delete_note', function(){


		var el = jQuery(this);

			var response_z = $.ajax({ type: "POST",   
					url: '/modules/advancedordernotes/ajax_controller.php',  
					cache: false,
				 	data: {  action: 'remove_order_note', id_note: $(this).attr('rel'), token: $('#aon_token').val()}, 
					async: true,
					success: function(data) 
					{
						el.closest('tr').remove();
					
					}
					
					}).responseText;

		return false;
	});


	$('body').on('click', '.edit_aon',function(){

		jQuery(this).closest('tr').find('.aon_element').toggleClass('hidden_el');
		return false;
	});





	$('body').on('click', '.aon_update_status',function(){

		var rel = jQuery(this).attr('rel');
		var el = jQuery(this);
		console.log($('.aon_update_status_'+rel).val());
		var response_z = $.ajax({ type: "POST",   
					url: '/modules/advancedordernotes/ajax_controller.php',  
					cache: false,
				 	data: {  action: 'update_status', id_note: rel, status: $('.aon_update_status_'+rel).val(), hidden_id_employee: $('#hidden_id_employee').val(), token: $('#aon_token').val() }, 
					async: true,
					success: function(data) 
					{
					    el.closest('tr').find('.aon_element').toggleClass('hidden_el');
					    el.closest('tr').find('.note_status').html($('.aon_update_status_'+rel+'  option:selected').text());
					    
					}
					
					}).responseText;



		return false;
	});


	$('body').on('click', '.aon_update_textarea',function(){

		var rel = jQuery(this).attr('rel');
		var el = jQuery(this);

		var response_z = $.ajax({ type: "POST",   
					url: '/modules/advancedordernotes/ajax_controller.php',  
					cache: false,
				 	data: {  action: 'update_textarea', id_note: rel, note: $('.textarea_'+rel).val(), hidden_id_employee: $('#hidden_id_employee').val(), token: $('#aon_token').val() }, 
					async: true,
					success: function(data) 
					{
					    el.closest('tr').find('.aon_element').toggleClass('hidden_el');
					    el.closest('tr').find('.aon_text').html($('.textarea_'+rel).val());
					    
					}
					
					}).responseText;



		return false;
	});



	$('body').on('click', '#addOrderNotecustomer',function(){

			if($('#ordernotes_comment').val().length < 3){
				alert('Note cannot be empty');
				return false;
			}

			var response_z = $.ajax({ type: "POST",   
					url: '/modules/advancedordernotes/ajax_controller.php',  
					cache: false,
				 	data: {  action: 'add_order_note_customer', note: $('#ordernotes_comment').val(), hidden_id_customer: $('#hidden_id_customer').val(), hidden_id_employee: $('#hidden_id_employee').val(), note_status: $('#note_statuses').val(), token: $('#aon_token').val() }, 
					async: true,
					success: function(data) 
					{
						$('#order_notes').append(data);
						$('#ordernotes_comment').val('');
						$('.noorders').remove();
					}
					
					}).responseText;

		return false;
	});





});