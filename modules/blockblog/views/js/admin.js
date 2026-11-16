/**
 * StorePrestaModules SPM LLC.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 *
 /*
 *
 * @author    StorePrestaModules SPM
 * @category content_management
 * @package blockblog
 * @copyright Copyright StorePrestaModules SPM
 * @license   StorePrestaModules SPM
 */

function blockblog_list(id,action,value,type_action,token){

    if(action == 'active') {

        if(type_action == 'is_comments' || type_action == 'is_fbcomments' || type_action == 'is_slider' || type_action == 'is_active' || type_action == 'gallery'){
            $('.'+type_action + id).html('<img src="../img/admin/../../modules/blockblog/views/img/loader.gif" />');
        } else {
            $('#activeitem' + id).html('<img src="../img/admin/../../modules/blockblog/views/img/loader.gif" />');
        }
    }

    $.post(ajax_link_blockblog,
        { id:id,
            action_custom:action,
            value: value,
            type_action: type_action,
            token: token,
            ajax : true,
            controller : 'AdminBlockblogajax',
            action : 'BlockblogAjax',
        },
        function (data) {
            if (data.status == 'success') {


                var data = data.params.content;

                var text_action = '';
                if(type_action == 'category'){
                    text_action = 'category';
                } else if(type_action == 'is_active'){
                    text_action = 'post';
                } else if(type_action == 'comment'){
                    text_action = 'comment';
                } else if(type_action == 'is_comments'){
                    text_action = 'comments for post';
                } else if(type_action == 'is_fbcomments'){
                    text_action = 'Facebook comments for post';
                } else if(type_action == 'gallery'){
                    text_action = 'item';
                } /*else if(type_action == 'galleryfeatured'){
                    text_action = 'item';
                }*/

                if(action == 'active'){

                    if(type_action == 'is_comments' || type_action == 'is_fbcomments' || type_action == 'is_active' || type_action == 'is_slider' || type_action == 'gallery'){
                        $('.' + type_action + id).html('');
                    } else {
                        $('#activeitem' + id).html('');
                    }
                    if(value == 0){
                        var img_ok = 'ok';
                        var action_value = 1;
                    } else {
                        var img_ok = 'no_ok';
                        var action_value = 0;
                    }
                    var html = '<span class="label-tooltip" data-original-title="Click here to activate or deactivate '+text_action+' on your site" data-toggle="tooltip">'+
                            '<a href="javascript:void(0)" onclick="blockblog_list('+id+',\'active\', '+action_value+',\''+type_action+'\',\''+token+'\');" style="text-decoration:none">'+
                        '<img src="../img/admin/../../modules/blockblog/views/img/'+img_ok+'.gif" />'+
                        '</a>'+
                    '</span>';

                    if(type_action == 'is_comments' || type_action == 'is_fbcomments' || type_action == 'is_active' || type_action == 'is_slider' || type_action == 'gallery'){

                        $('.'+type_action+id).html(html);
                    } else {
                        $('#activeitem'+id).html(html);
                    }

                    // add code for alert message //
                    if(type_action == 'is_comments'){
                        var success_text_action = 'Standart comments for ';
                    } else if(type_action == 'is_fbcomments'){
                        var success_text_action = 'Facebook comments for ';
                    } else if(type_action == 'is_slider'){
                        var success_text_action = 'Slider for ';
                    } else {
                        var success_text_action = '';
                    }


                    if(value == 0) {
                        var message_active = 'activated';
                    } else {
                        var message_active = 'deactivated';
                    }
                    $('.bootstrap .alert').remove();
                    $('.custom-success-message').remove();
                    var html_success = '<div class="custom-success-message flash-message-list alert alert-success">'+
                        '<ul>'+
                        '<li>'+success_text_action+'Item #'+id+' successfully '+message_active+'.</li>'+
                        '</ul>'+
                        '</div>';
                    if($('#form-blogpost').length > 0)
                        $('#form-blogpost').before(html_success);
                    if($('#form-blogcategory').length > 0)
                        $('#form-blogcategory').before(html_success);
                    if($('#form-blog_comments').length > 0)
                        $('#form-blog_comments').before(html_success);
                    if($('#form-bloggallery').length > 0)
                        $('#form-bloggallery').before(html_success);
                    // add code for alert message //




                }

            } else {
                alert(data.message);

            }
        }, 'json');
}




function delete_avatar(item_id,id_customer,is_admin){
    if(confirm("Are you sure you want to remove this item?"))
    {
        $('.avatar-form').css('opacity',0.5);
        $.post(ajax_link_blockblog, {
                action:'deleteimgava',
                item_id : item_id,
                id_customer: id_customer,
                is_admin: is_admin
            },
            function (data) {
                if (data.status == 'success') {
                    $('.avatar-form').css('opacity',1);
                    $('.avatar-form').html('');
                    $('.avatar-form').html('<img src = "../modules/blockblog/views/img/avatar_m.gif" />');


                } else {
                    $('.avatar-form').css('opacity',1);
                    alert(data.message);
                }

            }, 'json');
    }

}





function initAccessoriesAutocomplete_blockblog(){
    $('document').ready( function() {

        if(blockblog_is_ps1760 == 1){
            var autocomplete_blockblog_url = 'index.php?token='+blockblog_token+'&ajax=1&controller=AdminProducts&action=productsList';
        } else {
            var autocomplete_blockblog_url ='ajax_products_list.php?token='+blockblog_token;
        }

        $('#product_autocomplete_input')
            .autocomplete(
            //'ajax_products_list.php?token='+blockblog_token,

            //'index.php?token='+blockblog_token+'&ajax=1&controller=AdminProducts&action=productsList',

            autocomplete_blockblog_url,

            {

                minChars: 1,
                autoFill: true,
                max:20,
                matchContains: true,
                mustMatch:true,
                scroll:false,
                cacheLength:0,

                formatItem: function(item) {
                    return item[1]+' - '+item[0];
                }
            }).result(addAccessory);

        $('#product_autocomplete_input').setOptions({
            extraParams: {
                excludeIds : getAccessoriesIds(),

            }
        });



    });
}


function addAccessory(event, data, formatted)
{
    if (data == null)
        return false;
    var productId = data[data.length - 1];
    var productName = data[0];


    var $divAccessories = $('#divAccessories');
    var $inputAccessories = $('#inputAccessories');
    var $product_autocomplete_input = $('#product_autocomplete_input');

     $product_autocomplete_input.val('');
    $product_autocomplete_input.val(productName);

    $inputAccessories.val('');
    $inputAccessories.val(productId);


}

function getAccessoriesIds()
{
    if ($('#inputAccessories').val() === undefined) return '';
    if ($('#inputAccessories').val() == '') return ',';
    ids = $('#inputAccessories').val().replace(/\-/g,',');
    return ids;
}





// remove add new comment button //
$('document').ready( function() {

    $('#desc-blog_comments-new').css('display','none');


});
// remove add new comment button //


