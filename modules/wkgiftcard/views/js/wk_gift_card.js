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
    setActiveCssToLabels();
    prestashop.on("updateProduct", function (event) {
        setTimeout(function () {
            let id_product = $("#product_page_product_id").val();
            $.ajax({
                url: wkGiftCartLink,
                type: "post",
                async: true,
                dataType: "json",
                data: {
                    ajax: true,
                    action: "getGiftCardCustomizableFields",
                    token: wk_gift_card_token,
                    id_product: id_product,
                },
                success: function (response) {
                    if (typeof response.giftCardTpl != 'undefined' && response.giftCardTpl != '') {
                        $('#wk_product_customization_field').html(response.giftCardTpl);
                    }
                },
            });
        }, 600)
    });

    $('.wk_add_to_cart').hide();
    addGiftProductTouchSpin();
    let giftCardData = false;

    $(document).on('click', '#gift_button', function (e) {
        e.preventDefault();
        if ($('#wk-other-tab').hasClass('active')) {
            if ($.trim($('#gift_recipient_name').val()) == '') {
                $.growl.error({ title: '', message: wk_enter_name });
                return false;
            }
            if ($.trim($('#gift_recipient_email').val()) == '') {
                $.growl.error({ title: '', message: wk_enter_email });
                return false;
            }
            if ($.trim($('#gift_your_name').val()) == '') {
                $.growl.error({ title: '', message: wk_enter_from_name });
                return false;
            }
            if ($('#is_dynamic_price_gift_card').val() == '1') {
                var price = $('#gift_card_price').val();
                if (price < 0) {
                    $.growl.error({ title: '', message: invalidPriceErr });
                    return false;
                }
            }
            if ($('#gift_recipient_message').val().length > message_allowed_char) {
                $.growl.error({ title: '', message: invalidmessagelength });
                return false;
            }

            giftCardData = retriveGiftCardFormData();
        } else {
            giftCardData = retriveGiftCardFormData(true);
        }

        $('#gift_button').attr('disabled', false);
        var quantity = $('#gift_product_quantity_wanted').val();
        $.ajax({
            url: wkGiftCartLink,
            data: {
                token: wk_gift_card_token,
                action: 'add_gift_card_product_to_cart',
                is_dynamic_price_gift_card: $('#is_dynamic_price_gift_card').val(),
                name: giftCardData?.name,
                from_name: giftCardData?.from_name,
                email: giftCardData?.email,
                message: giftCardData?.message,
                date: $('#gift_card_delivery_date').val(),
                price: $('#gift_card_price').val(),
                quantity: quantity,
                id_product: $('#product_page_product_id').val(),
            },
            method: 'POST',
            dataType: 'json',
            success: function (result) {
                if (result.status == 'ok') {
                    $('#gift_button').attr('disabled', false);
                    $('#quantity_wanted').val($('#gift_product_quantity_wanted').val());
                    $(".add-to-cart")[0].click();
                    if (result.available_qty <= 0) {
                        $('#gift_button').attr('disabled', 'disabled');
                        $.growl.error({ title: '', message: nogiftcardavailable });
                    }
                }
                if (result.errors != 'undefined') {
                    var errorHtml = '';
                    $(result.errors).each(function (key, error) {
                        errorHtml += error + '</br>';
                    });
                    if (errorHtml != '') {
                        $.growl.error({ title: '', message: errorHtml });
                    }
                    if (result.reload) {
                        location.reload();
                    }
                }
            }
        });
    });

    if (typeof prestashop !== 'undefined') {
        prestashop.on(
            'updateCart',
            function (event) {
                if (typeof (event.reason.linkAction) != "undefined" && event.reason.linkAction == 'delete-from-cart') {
                    $.ajax({
                        url: wkGiftCartLink,
                        data: {
                            token: wk_gift_card_token,
                            action: 'delete_gift_card_product_to_cart',
                            id_product: event.reason.idProduct,
                        },
                        method: 'POST',
                        dataType: 'json',
                        success: function (result) {

                        }
                    });
                }
            }
        );
    }

    $(document).on("focus", "#gift_card_delivery_date", function () {
        var dlocale = prestashop.language.iso_code;
        if (typeof ($.datepicker.regional[dlocale]) == 'undefined') {
            dlocale = '';
        }
        $("#gift_card_delivery_date").datepicker({
            showOtherMonths: true,
            dateFormat: 'dd-mm-yy',
            minDate: 0,
        });
        $.datepicker.setDefaults($.datepicker.regional[dlocale]);
    });

    // Filter Gift card Product with price
    $('.gift_card_price').on('click', function (e) {
        e.preventDefault();
        let priceType = $(this).attr('data-price-name');
        const url = new URL(window.location);
        url.searchParams.set('type', priceType);
        window.location.replace(url.toString());
    })

    // Filter Gift card product with Tag.
    $('.tag_filter_btn').on('click', function (e) {
        e.preventDefault();
        // $('.tag_filter_btn').removeClass('active');
        const url = new URL(window.location);
        let q = url.searchParams.get('q');
        let type = url.searchParams.get('type');
        let p = url.searchParams.get('p');
        let orderby = url.searchParams.get('orderby');
        let orderway = url.searchParams.get('orderway');

        if (!q) {
            q = false;
        } else if (!type) {
            type = 0;
        } else if (!p) {
            p = false;
        } else if (!orderby) {
            orderby = ''
        } else if (!orderway) {
            orderway = ''
        }

        let tag = $(this).attr('data-tag-name');

        $.ajax({
            url: wkGiftCartListLink,
            data: {
                token: wk_gift_card_token,
                action: 'filterGiftCardByTag',
                tag,
                q,
                type,
                p,
                orderby,
                orderway
            },
            method: 'POST',
            dataType: 'json',
            success: function (result) {
                if (result) {
                    $('#giftCardProductDetails').html('');
                    $('#giftCardProductDetails').html(result.product_filter_tpl);
                    $('.tag_filter_btn').removeClass('tag_active');
                    $(`a[data-tag-name='${tag}']`).addClass('tag_active');
                }
            }
        });
    })
});
function addGiftProductTouchSpin() {
    $.each($('#gift_product_quantity_wanted'), function (index, spinner) {
        $(spinner).TouchSpin({
            verticalbuttons: true,
            verticalupclass: 'material-icons touchspin-up',
            verticaldownclass: 'material-icons touchspin-down',
            buttondown_class: 'btn btn-touchspin js-touchspin js-increase-product-quantity',
            buttonup_class: 'btn btn-touchspin js-touchspin js-decrease-product-quantity',
            min: parseInt($(spinner).attr('min'), 10),
            max: 1000000
        });
    });
}

function setActiveCssToLabels() {
    const url = new URL(window.location);
    let type = url.searchParams.get('type');
    if (type != null) {
        $(`a[data-price-name='${type}']`).addClass('tag_active');
    }
}

function retriveGiftCardFormData(self = false) {
    if (self) {
        return {
            name: $('#gift_recipient_name_self').val(),
            from_name: $('#gift_your_name_self').val(),
            email: $('#gift_recipient_email_self').val(),
            message: $('#gift_recipient_message_self').val(),
        }
    }

    return {
        name: $('#gift_recipient_name').val(),
        from_name: $('#gift_your_name').val(),
        email: $('#gift_recipient_email').val(),
        message: $('#gift_recipient_message').val(),
    }




}
