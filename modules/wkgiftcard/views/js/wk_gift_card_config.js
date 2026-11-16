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
    $('input[name="WK_GIFT_CARD_SEND_PDF_IN_EMAIL"]').on('change', function () {
        if ($(this).val() == 1) {
            $('input[name=WK_GIFT_CARD_PRODUCT_PDF_IMG]').parents('.form-group').slideDown(300);

            if ($('#WK_GIFT_CARD_PRODUCT_PDF_IMG_off').is(':checked')) {
                $('.wkpdfimg').slideDown(300);
            } else {
                $('.wkpdfimg').slideUp(300);
            }
        } else {
            $('input[name=WK_GIFT_CARD_PRODUCT_PDF_IMG]').parents('.form-group').slideUp(300);
            $('.wkpdfimg').slideUp(300);
        }
    });

    if ($('input[name="WK_GIFT_CARD_SEND_PDF_IN_EMAIL"]:checked').val() == 1) {
        $('.wkpdfimg').show();
    } else {
        $('.wkpdfimg').hide();
    }

    $('.preview_image').next('p').children('.btn-default').on('click', function (e) {
        e.preventDefault();
        $.confirm({
            columnClass: 'col-md-4 col-md-offset-4',
            title: confirm_title + '!',
            content: confirm_description + '!',
            columnClass: 'small',
            buttons: {
                confirm: function () {
                    window.location.href = e.currentTarget.getAttribute("href");
                },
                cancel: function () {
                },

            }
        });
    });

    $('[data-toggle="tooltip"]').tooltip();

    if ($('#WK_GIFT_CARD_PRODUCT_PDF_IMG_off').is(':checked')) {
        $('.wkpdfimg').show();
    } else {
        $('.wkpdfimg').hide();
    }

    $('input[name=WK_GIFT_CARD_PRODUCT_PDF_IMG]').on('change', function () {
        if ($(this).val() == 1) {
            $('.wkpdfimg').slideUp(200);
        } else {
            $('.wkpdfimg').slideDown(200);
        }
    });
});