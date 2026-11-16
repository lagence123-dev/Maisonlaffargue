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

function delete_img(item_id){

    if(confirm("Are you sure you want to remove this item ?"))
    {
        $('#post_images_list').css('opacity',0.5);
        $.post(ajax_link_blockblog, {
                action:'deleteimg',
                item_id : item_id
            },
            function (data) {
                if (data.status == 'success') {
                    $('#post_images_list').css('opacity',1);
                    $('#post_images_list').html('');

                } else {
                    $('#post_images_list').css('opacity',1);
                    alert(data.message);
                }

            }, 'json');
    }

}