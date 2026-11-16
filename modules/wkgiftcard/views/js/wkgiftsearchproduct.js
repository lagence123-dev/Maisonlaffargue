/**
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
*/

$(document).ready(function () {
	$('.gift_card_config_tab').hide();
	$('.tab-row.active').removeClass('active');
	$('#gift_card_config_' + current_config_tab).show();
	$('#gift_card_config_tab_link_' + current_config_tab).parent().addClass('active');
	$('#current_config_tab').val(current_config_tab);

	tinySetup({
		editor_selector: "wk_tinymce",
	});
	$('.wk-giftcard-datepicker').datepicker({
		dateFormat: 'yy-mm-dd',
		minDate: 0,
	});
	var elementSearchAjax = null;
	var selectedProduct = null;
	$('#gift_product').on('keyup', function () {
		if ($(this).val().length < 3) {
			return;
		}
		var product_id = $('#id_ps_product').val();
		selectedProduct = null;
		if ($(this).val().trim().length) {
			var suggestionElement = $('#wk_giftProd_suggestion_cont');
			suggestionElement.hide().empty();

			if (elementSearchAjax) {
				elementSearchAjax.abort();
			}
			elementSearchAjax = $.ajax({
				url: ajax_link,
				type: 'POST',
				dataType: 'json',
				data: {
					q: $(this).val().trim(),
					product_id: product_id,
					action: 'getGiftProduct',
					ajax: true,
					wk_token: wk_gift_card_token
				},
				success: function (result) {
					if (typeof result.satatus != 'undefined') {
						$.growl.error({ title: "", message: result.errors });
					} else {
						var html = '';
						if (Object.keys(result).length) {
							var excludeIds = getSelectedIds();
							$.each(result, function (index, element) {
								if ($.inArray(element.id.toString(), excludeIds) !== -1) {
								} else {
									html += "<li class='wk_giftProd_suggestion_list' data-id-product='" + element.id + "' data-product-image='" + element.image + "' data-product-name='" + element.name + "' data-product-ref='" + element.ref + "'>";
									html += "<img src='" + element.image + "'> <span>" + element.name + "</span>";
									html += "</li>";
								}
							});
						}
						if (html) {
							suggestionElement.append(html).show();
						}
					}
				},
				error: function (error) {
					console.log(error);
				}
			});
		}
	});

	$("body").on("click", ".wk_giftProd_suggestion_list", function () {
		selectedProduct = $(this);
		$("#gift_product").val($(this).attr("data-product-name"));
		$("#wk_giftProd_suggestion_cont").hide().empty();
	});

	$('#add_select_item').on('click', function () {
		if (selectedProduct) {
			$('.pack-empty-warning').hide();

			var html = '';
			html += '<div class="col-md-3 mb-3">';
			html += '<div class="wk-gift-prod-cont">';
			html += '<img src="' + selectedProduct.attr('data-product-image') + '">';
			html += '<h4>' + selectedProduct.attr('data-product-name') + '</h4>';
			html += '<span>' + selectedProduct.attr('data-product-ref') + '</span>';
			html += '<button class="btn btn-default deleteGiftProd" type="button"><i class="material-icons">delete</i></button>';
			html += '<input type="hidden" class="pspk_id_prod" name="pspk_id_prod[]" value="' + selectedProduct.attr('data-id-product') + '">';
			html += '</div>';
			html += '</div>';

			$("#divItems").append(html);
			$("#gift_product").val('');
			selectedProduct = null;
		} else {
			$.growl.error({ title: "", message: prod_err });
		}
	});

	$('#gift_image-selectbutton').click(function (e) {
		$('#gift_image').trigger('click');
	});

	$('#gift_image-name').click(function (e) {
		$('#gift_image').trigger('click');
	});

	$('#gift_image').change(function (e) {
		if ($(this)[0].files !== undefined) {
			if (this.files[0].size > maxSizeAllowed * 1000000) {
				$.growl.error({ title: "", message: filesizeError });
				setTimeout(function () {
					location.reload();
				}, 1000);
			}
			var files = $(this)[0].files;
			var name = '';

			$.each(files, function (index, value) {
				name += value.name + ', ';
			});

			$('#gift_image-name').val(name.slice(0, -2));
		}
		else // Internet Explorer 9 Compatibility
		{
			var name = $(this).val().split(/[\\/]/);
			$('#gift_image-name').val(name[name.length - 1]);
		}
	});

	$('body').on('click', '.deleteGiftProd', function (e) {
		$(this).parent().parent().remove();
	});

	//my js
	var disableGiftcard = $('#id_page_display').attr('data-disable-giftcard');
	if (disableGiftcard) {
		$("#id_page_display").on("click", function (e) {
			$('input[type=radio]').attr('disabled', true);
			$('#id_giftcard_voucher').show().text(messageShow).css('color', 'red');
		});
	}
	$("#id_giftcard_voucher").hide();
	if ($("#type_discount").val() == 'percentage') {
		$('#discount_percentage').show();
		$('#discount_amount').hide();
	} else {
		$('#discount_percentage').hide();
		$('#discount_amount').show();
	}

	if ($('input[name="is_giftcard"]:checked').val() == 1) {
		$("#id_giftcard_voucher").show();
	} else {
		$("#id_giftcard_voucher").hide();
	}

	if ($('input[name="is_select_product"]:checked').val() == 1) {
		$("#id_choose_product").show();
	} else {
		$("#id_choose_product").hide();
	}

	if ($('input[name="is_select_category"]:checked').val() == 1) {
		$("#id_choose_category").show();
	} else {
		$("#id_choose_category").hide();
	}

	if ($('input[name="dynamic_price"]:checked').val() == 1) {
		$("#price_div").hide();
	} else {
		$("#price_div").show();
	}

	$(".datepicker").datepicker({
		dateFormat: 'yy-mm-dd'
	});
	$('#wk_pd_name').hide();

	$('#type_discount').on('change', function () {
		if (this.value == 'percentage') {
			$('#discount_percentage').show();
			$('#discount_amount').hide();
		} else {
			$('#discount_percentage').hide();
			$('#discount_amount').show();
		}
	});

	//start category tree js
	var id_gift_card = $("#idTable").val();
	var catIds = $('#product_category').val();
	$('#categorycontainer').jstree({
		"checkbox": {
			"keep_selected_style": false, //if true then selection will display highlighted
			"three_state": false, //if true then selection will work according to parent
			"whole_node": true //if true then whole node (checkbox and content will work with click event)
		},
		"plugins": ["checkbox"],
		'core': {
			'data': {
				"url": admin_gift_card_url,
				"dataType": "json",
				"data": function (node) {
					return {
						"catsingleId": node.id,
						"id": id_gift_card,
						"catIds": catIds,
						"token": $('#wk-static-token').val(),
						"ajax": true,
						"action": "productCategory"
					};
				}
			}
		}
	})
		.on('changed.jstree', function (e, data) { //When category changed then default category options will change.
			$('#default_category').html('');
			var rawCheckedID = [];
			var default_category_html = '';
			var i, j;
			for (i = 0, j = data.selected.length; i < j; i++) {

				var selected_category_id = data.instance.get_node(data.selected[i]).id;
				var selected_category_name = data.instance.get_node(data.selected[i]).text;
				rawCheckedID.push(selected_category_id);
				default_category_html += '<option value="' + selected_category_id + '"id="default_cat' + selected_category_id + '" name="' + selected_category_name + '" ';

				if (typeof defaultIdCategory !== 'undefined' && defaultIdCategory == selected_category_id) {
					default_category_html += 'Selected="selected"';
				}

				default_category_html += '>' + selected_category_name + '</option>';
			}
			$('#default_category').html(default_category_html);
			//get all checked category value in a input hidden type name 'product_category'
			$('#product_category').val(rawCheckedID.join(","));
		});

	let catConIds = $('#product_category_condition').val();
	$('#categorycontainerconditoin').jstree({
		"checkbox": {
			"keep_selected_style": false, //if true then selection will display highlighted
			"three_state": false, //if true then selection will work according to parent
			"whole_node": true //if true then whole node (checkbox and content will work with click event)
		},
		"plugins": ["checkbox"],
		'core': {
			'data': {
				"url": admin_gift_card_url,
				"dataType": "json",
				"data": function (node) {
					return {
						"catsingleId": node.id,
						"id": id_gift_card,
						"catIds": catConIds,
						"token": $('#wk-static-token').val(),
						"ajax": true,
						"action": "productCategory"
					};
				}
			}
		}
	}).on('changed.jstree', function (e, data) { //When category changed then assign cate.
		$('#product_category_condition').val('');
		let category_ids = [];
		var i, j;
		for (i = 0, j = data.selected.length; i < j; i++) {
			var selected_category_id = data.instance.get_node(data.selected[i]).id;
			category_ids.push(selected_category_id);
		}
		//get all checked category value in a input hidden type name 'product_category_condition'
		$('#product_category_condition').val(category_ids.join(','));
	});

	$(document).on("click", '.submitGiftCardProduct', function (e) {
		var button = $(this);
		setTimeout(function () {
			button.attr('disabled', true);
		}, 100)
	});
});

function getSelectedIds() {
	var ints = new Array();
	if ($('.pspk_id_prod').length > 0) {
		$.each($('.pspk_id_prod'), function (index, element) {
			ints.push($(element).val());
		})
	}

	return ints;
}

function showGiftCardLangField(lang_iso_code, id_lang) {
	$('.wkgiftcard_lang_btn').html(lang_iso_code + ' <span class="caret"></span>');
	$('.gift_card_name_all').hide();
	$('#gift_card_name_' + id_lang).show();
	$('.wk_text_field_all').hide();
	$('.wk_text_field_' + id_lang).show();
	// $('.gift_card_short_desc').hide();

	// $('#short_description_'+id_lang).show();
}

function productFormatResult(item) {
	itemTemplate = "<div class='media'>";
	itemTemplate += "<div class='pull-left'>";
	itemTemplate += "<img class='media-object' width='40' src='" + item.image + "' alt='" + item.name + "'>";
	itemTemplate += "</div>";
	itemTemplate += "<div class='media-body'>";
	itemTemplate += "<h4 class='media-heading'>" + item.name + "</h4>";
	itemTemplate += "<span>REF: " + item.ref + "</span>";
	itemTemplate += "</div>";
	itemTemplate += "</div>";
	return itemTemplate;
}

function productFormatSelection(item) {
	return item.name;
}

function handleSaveButtonsForPack() {
	// if no item left in the pack, disable save buttons
	if ($("#inputSelectItems").val() == "")
		return empty_pack_msg;
	return '';
}


function delSelectItem(id) {
	var reg = new RegExp('-', 'g');
	var regx = new RegExp('x', 'g');

	var input = $('#inputSelectItems');
	var name = $('#nameSelectItems');

	var inputCut = input.val().split(reg);
	var nameCut = name.val().split(new RegExp('¤', 'g'));

	input.val(null);
	name.val(null);
	for (var i = 0; i < inputCut.length; ++i)
		if (inputCut[i]) {
			var inputQty = inputCut[i].split(regx);
			if (inputQty[1] != id) {
				input.val(input.val() + inputCut[i] + '-');
				name.val(name.val() + nameCut[i] + '¤');
			}
		}

	var elem = $('.product-pack-item[data-product-id="' + id + '"]');
	elem.remove();
	if ($('.product-pack-item').length === 0) {
		$('.pack-empty-warning').show();
	}
}
//my js
function showpage(show) {
	if (show) {
		$("#id_giftcard_voucher").show();
	} else {
		$("#id_giftcard_voucher").hide();
	}
}

function showproduct(show) {

	if (show) {
		$("#id_choose_product").show();
	} else {
		$("#id_choose_product").hide();
	}
}

function showCategory(show) {
	if (show) {
		$('#id_choose_category').show();
	} else {
		$('#id_choose_category').hide();
	}
}

function showprice(show) {

	if (show) {
		$("#price_div").show();
	} else {
		$("#price_div").hide();
	}
}

function displayGiftCardModuleConfigTab(tab) {
	$('.gift_card_config_tab').hide();
	$('.tab-row.active').removeClass('active');
	$('#gift_card_config_' + tab).show();
	$('#gift_card_config_tab_link_' + tab).parent().addClass('active');
	$('#current_config_tab').val(tab);
}