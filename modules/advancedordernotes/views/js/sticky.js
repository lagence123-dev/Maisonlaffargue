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

function trigger_sticky_popup()
{
	var select_form = '<select id="aon_employee"><option value="0">All employees</div>';
	aon_employees.forEach(function(entry) {
		select_form += '<option value="'+entry.id+'">'+entry.name+'</option>';
  
	});
	 select_form += '</select>';
	jQuery('body').append('<div class="aon_overlay"><div class="aon_form"><img src="'+aon_url+'/modules/advancedordernotes/views/img/popup-closeButton.png" class="aon_close_sticky" /><div id="aon_do_area"><textarea placeholder="Your sticky note" id="aon_sticky_area"></textarea>'+select_form +aon_sticky_date+'<input class="button btn btn-primary pull-right" type="submit" name="addNote" id="add_sticky_note" value="'+add_note+'" style="float:right; "></div></div></div>');
}


function trigger_insert_sticky_notes()
{
	if(jQuery('#content').length >0)
		jQuery('#content').prepend(aon_sticky_notes);
	else
		jQuery('.content-div').prepend(aon_sticky_notes);	
}

jQuery(document).ready(function(){


	trigger_insert_sticky_notes();
	jQuery('body').append('<img src="'+aon_url+'/modules/advancedordernotes/views/img/sticky_add.jpg" class="aon_dd_sticky" />');
	jQuery('body').on('click', '.aon_dd_sticky', function(){

		trigger_sticky_popup();

	});



	jQuery('body').on('click', '.aon_close_sticky', function(){

		jQuery('.aon_overlay').remove();

	});

	jQuery('body').on('click', '#add_sticky_note', function(){

		if(jQuery('#aon_sticky_area').val().length < 3)
		{
			alert(sticky_error);
			return false;

		}

		var response_z = $.ajax({ type: "POST",   
							url: aon_url+'/modules/advancedordernotes/ajax_controller.php',  
							cache: false,
							data: { action: 'add_sticky_note',  token: aon_token_stickies, note: jQuery('#aon_sticky_area').val(), id_employee: jQuery('#aon_employee').val(), date_expire: jQuery('#aon_sticky_date').val()  }, 
							async: true,
							 success: function(data) 
							 {
							 	jQuery('#aon_do_area').html('<h2>'+order_note_created+'</h2>');		
								return;
							}
						}).responseText;


	});


	jQuery('body').on('click', '.aon_delete_sticky', function(){

		var r = confirm("Are you certain you want to delete the sticky note?");

		if (r == false) 
		{
		    return false;
		} 
		var el = jQuery(this);
		var response_z = $.ajax({ type: "POST",   
							url: aon_url+'/modules/advancedordernotes/ajax_controller.php',  
							cache: false,
							data: { action: 'delete_sticky_note',  token: aon_token_stickies, id_note: jQuery(this).attr('rel') }, 
							async: true,
							 success: function(data) 
							 {
							 	el.closest('.aon_sticky_note').remove();		
								return;
							}
						}).responseText;


	});



});