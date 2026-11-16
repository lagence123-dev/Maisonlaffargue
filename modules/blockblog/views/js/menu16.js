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

function init_tabs(id){
    $('document').ready( function() {



        if(id == 1){
            $('#navtabs16 a[href="#blogsettings"]').tab('show');
            $('#blognavtabs16 a[href="#urlrewrite"]').tab('show');

        }

        if(id == 17){
            $('#navtabs16 a[href="#blogsettings"]').tab('show');
            $('#blognavtabs16 a[href="#mainsettingsblog"]').tab('show');

        }

        if(id == 2){
            $('#navtabs16 a[href="#blogsettings"]').tab('show');
            $('#blognavtabs16 a[href="#categoriessettings"]').tab('show');

        }

        if(id == 3){
            $('#navtabs16 a[href="#blogsettings"]').tab('show');
            $('#blognavtabs16 a[href="#postssettings"]').tab('show');

        }

        if(id == 4){
            $('#navtabs16 a[href="#blogsettings"]').tab('show');
            $('#blognavtabs16 a[href="#commentssettings"]').tab('show');

        }

        if(id == 10){
            $('#navtabs16 a[href="#blogsettings"]').tab('show');
            $('#blognavtabs16 a[href="#fbcommentssettings"]').tab('show');

        }

        if(id == 5){
            $('#navtabs16 a[href="#blogsettings"]').tab('show');
            $('#blognavtabs16 a[href="#blockssettings"]').tab('show');

        }

        if(id == 6){
            $('#navtabs16 a[href="#blogsettings"]').tab('show');
            $('#blognavtabs16 a[href="#blockpositions"]').tab('show');

        }

        if(id == 7){
            $('#navtabs16 a[href="#blogsettings"]').tab('show');
            $('#blognavtabs16 a[href="#rssfeed"]').tab('show');

        }

        if(id == 8){
            $('#navtabs16 a[href="#blogsettings"]').tab('show');
            $('#blognavtabs16 a[href="#emailsettings"]').tab('show');

        }

        if(id == 9){
            $('#navtabs16 a[href="#blogsettings"]').tab('show');
            $('#blognavtabs16 a[href="#sitemap"]').tab('show');

        }

        if(id == 18){
            $('#navtabs16 a[href="#blogsettings"]').tab('show');
            $('#blognavtabs16 a[href="#sitemap"]').tab('show');

        }

        if(id == 11){
            $('#navtabs16 a[href="#blogsettings"]').tab('show');
            $('#blognavtabs16 a[href="#wordpress"]').tab('show');

        }

        if(id == 12){
            $('#navtabs16 a[href="#blogsettings"]').tab('show');
            $('#blognavtabs16 a[href="#avasettings"]').tab('show');

        }

        if(id == 13){
            $('#navtabs16 a[href="#blogsettings"]').tab('show');
            $('#blognavtabs16 a[href="#slider"]').tab('show');

        }


        if(id == 14){
            $('#navtabs16 a[href="#blogsettings"]').tab('show');
            $('#blognavtabs16 a[href="#gallery"]').tab('show');

        }

        if(id == 16){
            $('#navtabs16 a[href="#blogsettings"]').tab('show');
            $('#blognavtabs16 a[href="#myaccount"]').tab('show');

        }


        if(id == 15){
            $('#navtabs16 a[href="#autoposts"]').tab('show');

        }


        if(id == 19){
            $('#navtabs16 a[href="#blogsettings"]').tab('show');
            $('#blognavtabs16 a[href="#sidebarmenu"]').tab('show');
        }


        /* loyality program */
        if(id == 60){
            $('#navtabs16 a[href="#loyalty"]').tab('show');
            $('#loyalityprogram a[href="#loyalitysettings"]').tab('show');
        }

        if(id == 61){
            $('#navtabs16 a[href="#loyalty"]').tab('show');
            $('#loyalityprogram a[href="#voucherloyality"]').tab('show');
        }

        if(id == 62){
            $('#navtabs16 a[href="#loyalty"]').tab('show');
            $('#loyalityprogram a[href="#pointsactions"]').tab('show');
        }

        if(id == 63){
            $('#navtabs16 a[href="#loyalty"]').tab('show');
            $('#loyalityprogram a[href="#loyaltymessages"]').tab('show');
        }

        if(id == 64){
            $('#navtabs16 a[href="#loyalty"]').tab('show');
            $('#loyalityprogram a[href="#loyaltymy"]').tab('show');
        }

        if(id == 65){
            $('#navtabs16 a[href="#loyalty"]').tab('show');
            $('#loyalityprogram a[href="#loyaltyemails"]').tab('show');
        }

        if(id == 66){
            $('#navtabs16 a[href="#loyalty"]').tab('show');
            $('#loyalityprogram a[href="#loyaltyemailssubjects"]').tab('show');
        }
        /* loyality program */



    });
}



function sidebarmenu_subtab(tab_id){

    $('document').ready( function() {
        $('#sidebarmenutabs a[href="#'+tab_id+'"]').tab('show');
    });

}


function tabs_custom(id){



    if(id == 101){
        $('#navtabs16 a[href="#blogsettings"]').tab('show');
        $('#blognavtabs16 a[href="#sitemap"]').tab('show');

    }

    if(id == 102){
        $('#navtabs16 a[href="#blogsettings"]').tab('show');
        $('#blognavtabs16 a[href="#blockpositions"]').tab('show');

    }


    if(id == 6){
        $('#navtabs16 a[href="#info"]').tab('show');
    }


}





$(document).ready(function(){

    var array_positions_blockblog = ['desktop', 'mobile'];

    array_positions_blockblog.forEach(function(position_item_blockblog) {

        /* enable/disable positions  */
        $(document).on('click','#positionsblogtabs .blockblog-'+position_item_blockblog,function(){

            if(!$(this).hasClass('active'))
            {
                blockblog_position_enable_disable($(this).attr('data-blockblog'),1);
                $(this).addClass('active');
                $('#positionsblogtabs .blockblog-'+position_item_blockblog+' .'+$(this).attr('data-blockblog')+' input').prop('checked', true);
                return false;
            } else {

                blockblog_position_enable_disable($(this).attr('data-blockblog'),0);
                $(this).removeClass('active');
                $('#positionsblogtabs .blockblog-'+position_item_blockblog+' .'+$(this).attr('data-blockblog')+' input').prop('checked', false);
                return false;
            }

        });
        /* enable/disable positions */

    });


    var array_positions_desktop_blockblog = ['desktop', 'mobile'];
    array_positions_desktop_blockblog.forEach(function(position_item_blog_blockblog) {

        /* enable/disable positions for block  */
        $(document).on('click','#sidebar-positions-blockblog .blockblog-blog-'+position_item_blog_blockblog,function(){
            if(!$(this).hasClass('active'))
            {
                blockblog_position_blog_enable_disable($(this).attr('data-blockblog-blog'),1);
                $(this).addClass('active');
                $('#blockpositions .blockblog-'+position_item_blog_blockblog+' .'+$(this).attr('data-blockblog-blog')+' input').prop('checked', true);
                return false;
            } else {

                blockblog_position_blog_enable_disable($(this).attr('data-blockblog-blog'),0);
                $(this).removeClass('active');
                $('#blockpositions .blockblog-'+position_item_blog_blockblog+' .'+$(this).attr('data-blockblog-blog')+' input').prop('checked', false);
                return false;
            }

        });
        /* enable/disable positions for block */

    });





});



function blockblog_position_enable_disable(alias_position,is_active){

    $.post(ajax_link_blockblog, {
            action_custom:'enabledisableposition',
            alias_position : alias_position,
            is_active:is_active,
            token: token_blockblog,
            ajax : true,
            controller : 'Adminblockblogajax',
            action : 'blockblogAjax',

        },
        function (data) {
            if (data.status == 'success') {

                showSaveMessage('success');

            } else {
                showSaveMessage('error');
            }

        }, 'json');
}


function blockblog_position_blog_enable_disable(alias_position,is_active){

    $.post(ajax_link_blockblog, {
            action_custom:'enabledisablepositionblog',
            alias_position : alias_position,
            is_active:is_active,
            token: token_blockblog,
            ajax : true,
            controller : 'Adminblockblogajax',
            action : 'blockblogAjax',

        },
        function (data) {
            if (data.status == 'success') {

                showSaveMessage('success');

            } else {
                showSaveMessage('error');
            }

        }, 'json');
}



function showSaveMessage(type)
{
    if(type == 'success'){
        var message = success_message_blockblog;

        if(is17_blockblog == 1) {
            $.growl.notice({title: "", message: message});
        } else {

            $('.bootstrap .alert').remove();
            $('.custom-success-message').remove();
            var html_success = '<div class="custom-success-message flash-message-list alert alert-success">'+
                '<ul>'+
                '<li>'+message+'.</li>'+
                '</ul>'+
                '</div>';
            if($('.promo-file-spm').length > 0)
                $('.promo-file-spm').after(html_success);

            /*setInterval(function() {
             $(".custom-success-message").last().remove();
             }, 3000);*/
        }

    } else if(type == 'error'){

        var message = error_message_blockblog;


        if(is17_blockblog == 1) {
            $.growl.error({title: "", message: message});
        } else {

            $('.bootstrap .alert').remove();
            $('.custom-success-message').remove();
            var html_success = '<div class="custom-success-message flash-message-list alert alert-danger">'+
                '<ul>'+
                '<li>'+message+'.</li>'+
                '</ul>'+
                '</div>';



            if($('.promo-file-spm').length > 0)
                $('.promo-file-spm').after(html_success);


            /*setInterval(function() {
             $(".custom-success-message").last().remove();
             }, 3000);*/



        }

    }
}

