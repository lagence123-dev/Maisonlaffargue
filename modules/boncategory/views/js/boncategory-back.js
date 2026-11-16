/**
 * 2015-2021 Bonpresta
 *
 * Bonpresta Column Category
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the General Public License (GPL 2.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/GPL-2.0
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the module to newer
 * versions in the future.
 *
 *  @author    Bonpresta
 *  @copyright 2015-2021 Bonpresta
 *  @license   http://opensource.org/licenses/GPL-2.0 General Public License (GPL 2.0)
 */

$(document).ready(function(){

    $('.bootstrap .boncategory > tbody  tr  td.dragHandle').wrapInner('<div class="positions"/>');
    $('.bootstrap .boncategory > tbody  tr  td.dragHandle').wrapInner('<div class="dragGroup"/>');
    initAjaxTabs();

    $('.bon_category_icons option').each(function () {
        $(this).addClass($(this).val());
    });

    if ($("input").is("#switch_icon_off")) {
        if (document.getElementById("switch_icon_off").checked) {
            $('.bon_category_icons_type, .bon_category_icons').hide();
        }
    }


    var iconFamily = $('.bon_category_icons_type select').val();
    selectFamilyIcon();
    function selectFamilyIcon() {
        var iconFamily = $('.bon_category_icons_type select').val();
        var selectIcon = $('.bon_category_icons select');
        if (iconFamily == 'material_icons') {
            selectIcon.addClass('material_icons').removeClass('outicons')
            $('.bon_category_icons .material-icons').show();
            $('.bon_category_icons .fl-outicons').hide();
        } else if (iconFamily == 'outicons') {
            selectIcon.addClass('outicons').removeClass('material_icons')
            $('.bon_category_icons .fl-outicons').show();
            $('.bon_category_icons .material-icons').hide();
        }
    }

    $('.bon_category_icons_type #type_icon').change(function () {
        $('.bon_category_icons select').prop('selectedIndex', 0);
        selectFamilyIcon();
    });
    $('.bon_switch_icon input[type=radio][name=switch_icon]').change(function() {
        if (this.value == 1) {
            $('.bon_category_icons_type').show();
            $('.bon_category_icons').show();
        } else {
            $('.bon_category_icons_type').hide();
            $('.bon_category_icons').hide();
        }
    });
});



function initAjaxTabs(){
    $('.boncategory > tbody tr').each(function(){
        var id = $(this).find('td:first').text();
        $(this).attr('id', 'item_'+id.trim());
    });

    var $tabslides = $('.boncategory > tbody');

    $tabslides.sortable({
        cursor: 'move',
        items: '> tr',
        update: function(event, ui){
            $('.boncategory > tbody > tr').each(function(index){
                $(this).find('.positions').text(index + 1);
            });
        }
    }).bind('sortupdate', function() {
        var orders = $(this).sortable('toArray');
        $.ajax({
            type: 'POST',
            url: ajax_theme_url + '&ajax',
            headers: { "cache-control": "no-cache" },
            dataType: 'json',
            data: {
                action: 'updatepositionform',
                item: orders,
            },
            success: function(msg){
                if (msg.error) {
                    showErrorMessage(msg.error);
                    return;
                }
                showSuccessMessage(msg.success);
            }
        });
    });
};
