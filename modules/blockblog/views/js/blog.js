/**
 * 2011 - 2020 StorePrestaModules SPM LLC.
 *
 * MODULE blockblog
 *
 * @author    SPM <spm.presto@gmail.com>
 * @copyright Copyright (c) permanent, SPM
 * @license   Addons PrestaShop license limitation
 * @version   2.4.3
 * @link      http://addons.prestashop.com/en/2_community-developer?contributor=790166
 *
 * NOTICE OF LICENSE
 *
 * Don't use this module on several shops. The license provided by PrestaShop Addons
 * for all its modules is valid only once for a single shop.
 */

function blockblog_like_post(id,like){

    $('.post-like-'+id).css('opacity',0.5);

    $.post(ajax_url_blockblog, {
            action:'like',
            id : id,
            like : like
        },
        function (data) {
            if (data.status == 'success') {

                $('.post-like-'+id).css('opacity',1);

                var count = data.params.count;
                if(like == 1){
                    $('.post-like-'+id).html('');
                    $('.post-like-'+id).append('<i class="fa fa-thumbs-up fa-lg"></i>&nbsp;(<span class="the-number">'+count+'</span>)');

                    if($('.post-like1-'+id)){
                        $('.post-like1-'+id).html('');
                        $('.post-like1-'+id).append('<i class="fa fa-thumbs-up fa-lg"></i>&nbsp;(<span class="the-number">'+count+'</span>)');
                    }
                } else {
                    $('.post-unlike-'+id).html('');
                    $('.post-unlike-'+id).append('<i class="fa fa-thumbs-down fa-lg"></i>&nbsp;(<span class="the-number">'+count+'</span>)');

                    if($('.post-unlike1-'+id)){
                        $('.post-unlike1-'+id).html('');
                        $('.post-unlike1-'+id).append('<i class="fa fa-thumbs-down fa-lg"></i>&nbsp;(<span class="the-number">'+count+'</span>)');
                    }
                }


            } else {
                $('.post-like-'+id).css('opacity',1);
                alert(data.message);
            }

        }, 'json');
}



function go_page_blockblog(page,type,id_item,ajax_url_blockblog){


    if(ajax_url_blockblog === undefined){
        var ajax_url_blockblog = id_item;
    }

    if(id_item == "archive"){
        var m_blockblog = $('#blockblogm').val();
        var y_blockblog = $('#blockblogy').val();
    } else {
        var m_blockblog = '';
        var y_blockblog = '';
    }

    if(id_item == "search"){
        var search_blockblog = $('#blockblogsearch').val();
    } else {
        var search_blockblog = '';

    }

    if(id_item == "comments"){
        var post_id_blockblog = $('#blockblogpost_id').val();
    } else {
        var post_id_blockblog = '';
    }

    if(type == "author"){
        var blockblogauthor_id = $('#blockblogauthor_id').val();
    } else {
        var blockblogauthor_id = '';
    }

    if(type == "authors"){

        if(id_item == "search"){
            var searchu = $('#search').val();
            var blockblogsearch = $('#blockblogsearch').val();
            if(
                (searchu !== undefined && searchu !== '')
            ||
                (blockblogsearch !== undefined && blockblogsearch !== '')
            ){

                var search_blockblog = $('#search').val();

            }
        }
    }


    if(type == "myloyalty") {
        $('#points-list').css('opacity', 0.5);
    } else {
        $('#blog-items').css('opacity',0.5);
    }

    $.post(ajax_url_blockblog, {
            action:type,
            page : page,
            id_item: id_item,
            m: m_blockblog,
            y: y_blockblog,
            search: search_blockblog,
            post_id: post_id_blockblog,
            blockblogauthor_id: blockblogauthor_id

        },
        function (data) {
            if (data.status == 'success') {


                if(type == "myloyalty") {
                    $('#points-list').css('opacity', 1);
                    $('#points-list').html('');
                    $('#points-list').prepend(data.params.content);

                } else {
                    $('#blog-items').css('opacity',1);
                    $('#blog-items').html('');
                    $('#blog-items').prepend(data.params.content);

                }


                $('#page_nav').html('');
                $('#page_nav').prepend(data.params.page_nav);

                if(id_item == "comments"){
                    var id_scroll_to = "blogcomments";
                } else{

                    if (document.getElementById('center_column') instanceof Object){
                        var id_scroll_to = "center_column";
                    } else {
                        if (document.getElementById('content') instanceof Object){
                            var id_scroll_to = "content";
                        } else {
                            var id_scroll_to = "content-wrapper";
                        }


                    }



                }

                if(type == "authors") {


                    if (id_item == "search") {
                        var searchu = $('#search').val();
                        var blockblogsearch = $('#blockblogsearch').val();

                        if (
                            (searchu !== undefined && searchu !== '' && searchu !== 0)
                            ||
                            (blockblogsearch !== undefined && blockblogsearch !== '' && blockblogsearch !== 0)
                        ) {
                            $('#clear-search-users').removeClass('display-none');
                        }
                    } else {
                        $('#clear-search-users').addClass('display-none');
                        $('#search').val('');
                    }
                }

                $("html, body").animate({ scrollTop: $('#'+id_scroll_to).offset().top }, "slow");
                return false;


            } else {

                if(type == "myloyalty") {
                    $('#points-list').css('opacity', 1);
                } else {
                    $('#blog-items').css('opacity',1);
                }
                alert(data.message);
            }

        }, 'json');

}


function show_arch(id,column){
    for(i=0;i<100;i++){
        $('#arch'+i+column).hide(200);
    }
    $('#arch'+id+column).show(200);

}




/*
 blockblog_init_effects() - function only for lists views, allcomments, categories, postcomments, posts
 */

function blockblog_init_effects(){

    wow_item = new WOW(
        {
            boxClass:     "pl-animate",
            animateClass: "visible animated",
            offset:       100,
        }
    );

    wow_item.init();
}



/* only for blog post page for adding comment for blog post */

function trim_blockblog(str) {
    str = str.replace(/(^ *)|( *$)/,"");
    return str;
}


function field_gdpr_change_blockblog(){
    // gdpr
    var gdpr_blockblog = $('#psgdpr_consent_checkbox_'+blockblogid_module);

    var is_gdpr_blockblog = 1;

    if(gdpr_blockblog.length>0){

        if(gdpr_blockblog.prop('checked') == true) {
            $('.gdpr_module_'+blockblogid_module+' .psgdpr_consent_message').removeClass('error-label');
        } else {
            $('.gdpr_module_'+blockblogid_module+' .psgdpr_consent_message').addClass('error-label');
            is_gdpr_blockblog = 0;
        }

        $('#psgdpr_consent_checkbox_'+blockblogid_module).on('click', function(){
            if(gdpr_blockblog.prop('checked') == true) {
                $('.gdpr_module_'+blockblogid_module+' .psgdpr_consent_message').removeClass('error-label');
            } else {
                $('.gdpr_module_'+blockblogid_module+' .psgdpr_consent_message').addClass('error-label');
            }
        });

    }

    //gdpr

    return is_gdpr_blockblog;
}

function field_state_change_blockblog(field, state, err_text)
{
    field_gdpr_change_blockblog();

    var field_label = $('label[for="'+field+'"]');
    var field_div_error = $('#'+field);

    if (state == 'success')
    {
        field_label.removeClass('error-label');
        field_div_error.removeClass('error-current-input');
    }
    else
    {
        field_label.addClass('error-label');
        field_div_error.addClass('error-current-input');
    }
    document.getElementById('error_'+field).innerHTML = err_text;

}


function check_inpName_blockblog()
{

    var name_blockblog = trim_blockblog(document.getElementById('name-blockblog').value);

    if (name_blockblog.length == 0)
    {
        field_state_change_blockblog('name-blockblog','failed', blockblog_msg_name);
        return false;
    }
    field_state_change_blockblog('name-blockblog','success', '');
    return true;
}


function check_inpEmail_blockblog()
{

    var email_blockblog = trim_blockblog(document.getElementById('email-blockblog').value);

    if (email_blockblog.length == 0)
    {
        field_state_change_blockblog('email-blockblog','failed', blockblog_msg_em);
        return false;
    }
    field_state_change_blockblog('email-blockblog','success', '');
    return true;
}


function check_inpText_blockblog()
{

    var comment_blockblog = trim_blockblog(document.getElementById('comment-blockblog').value);

    if (comment_blockblog.length == 0)
    {
        field_state_change_blockblog('comment-blockblog','failed', blockblog_msg_comm);
        return false;
    }
    field_state_change_blockblog('comment-blockblog','success', '');
    return true;
}


function check_inpCaptcha_blockblog()
{


        var captcha_blockblog = trim_blockblog(document.getElementById('captcha-blockblog').value);

        if (captcha_blockblog.length != 6) {
            field_state_change_blockblog('captcha-blockblog', 'failed', blockblog_msg_cap);
            return false;
        }
        field_state_change_blockblog('captcha-blockblog', 'success', '');

        return true;

}

function check_inpRating_blockblog()
{
    var rating_blockblog = trim_blockblog(document.getElementById('rating-blockblog').value);
    if(rating_blockblog == 0){
        field_state_change_blockblog('rating-blockblog','failed', blockblog_msg_rate);
        return false;
    }
    field_state_change_blockblog('rating-blockblog','success', '');
    return true;
}


function blockblog_post_page_init(){
    //$('#name-blockblog').val('');
    //$('#email-blockblog').val('');
    $('#comment-blockblog').val('');
    $('#captcha-blockblog').val('');
    $('#rating-blockblog').val(0);
}


function add_comment(id_post){

    var is_name = check_inpName_blockblog();
    var is_comment = check_inpText_blockblog();
    var is_email = check_inpEmail_blockblog();
    if(blockblogis_captcha_com  && blockblogbcaptcha_type == 1) {
        var is_captcha = check_inpCaptcha_blockblog();
    } else {
        var is_captcha = true;
    }
    var is_rating = check_inpRating_blockblog();

    var is_gdpr_blockblog = field_gdpr_change_blockblog();

    if(is_name && is_comment && is_email && is_captcha && is_rating && is_gdpr_blockblog){

        var _name_review = $('#name-blockblog').val();
        var _email_review = $('#email-blockblog').val();
        var _text_review = $('#comment-blockblog').val();
        if(blockblogis_captcha_com && blockblogbcaptcha_type == 1) {
            var _captcha = $('#captcha-blockblog').val();
        } else {
            var _captcha = '';
        }
        var _rating = $('#rating-blockblog').val();



        $('#leaveComment').css('opacity','0.5');
        $('#leaveComment input.btn-custom').attr('disabled','disabled');

        $.post(ajax_url_blockblog,
            {action:'addcomment',
                name:_name_review,
                email:_email_review,
                id_post:id_post,
                captcha:_captcha,
                rating: _rating,
                text_review:_text_review,
                form_data: $('#commentform').serialize()
            },
            function (data) {
                if (data.status == 'success') {

                    //$('#name-blockblog').val('');
                    //$('#email-blockblog').val('');
                    $('#comment-blockblog').val('');
                    $('#captcha-blockblog').val('');
                    $('#rating-blockblog').val(0);

                    $('#leaveComment').hide();
                    $('.leaveComment-title').hide();
                    $('#succes-comment').show();



                    $('#leaveComment').css('opacity','1');
                    $('#leaveComment input.btn-custom').removeAttr('disabled');


                } else {

                    $('#leaveComment').css('opacity','1');
                    $('#leaveComment input.btn-custom').removeAttr('disabled');

                    var error_type = data.params.error_type;

                    if(error_type == 1){
                        field_state_change_blockblog('name-blockblog','failed', blockblog_msg_name);
                    } else if(error_type == 2){
                        field_state_change_blockblog('email-blockblog','failed', blockblog_msg_em);
                    } else if(error_type == 3){
                        field_state_change_blockblog('comment-blockblog','failed', blockblog_msg_comm);
                    } else if(error_type == 4){
                        field_state_change_blockblog('captcha-blockblog','failed', blockblog_msg_cap);
                    }else if(error_type == 5){
                        field_state_change_blockblog('rating-blockblog','failed', blockblog_msg_rate);
                    } else {
                        alert(data.message);
                    }

                    if(blockblogis_captcha_com && blockblogbcaptcha_type == 1) {
                        var count = Math.random();
                        document.getElementById('secureCodReview').src = "";
                        document.getElementById('secureCodReview').src = blockblogcaptcha_url + "?re=" + count;
                    }



                }
            }, 'json');
    }
}

/* only for blog post page for adding comment for blog post */






$(document).ready(function(){


    /* if page contains #blogcomments, then go to id="blogcomments" */
    if(window.location.href.indexOf('#') != -1){
        var vars = [], hash_blockblog = '';
        var hashes_blockblog = window.location.href.slice(window.location.href.indexOf('#') + 1);

        for(var i = 0; i < hashes_blockblog.length; i++)
        {
            hash_blockblog += hashes_blockblog[i];
        }

        if(hash_blockblog == "blogcomments") {
            setTimeout(function(){
                $("html, body").animate({scrollTop: $('#' + hash_blockblog).offset().top}, "slow");
            }, 500);
        }

    }
    /* if page contains #blogcomments, then go to id="blogcomments" */




if ($('.owl_blog_related_products_type_carousel ul').length > 0) {


    if (typeof $('.owl_blog_related_products_type_carousel ul').owlCarousel === 'function') {

        $('.owl_blog_related_products_type_carousel ul').owlCarousel({
            items: blockblog_number_product_related_slider,
            loop: true,
            responsive: true,
            nav: true,
            navRewind: false,
            margin: 10,
            dots: true,

            lazyLoad: true,
            lazyFollow: true,
            lazyEffect: "fade",
        });
    }
}


    if ($('.owl_blog_related_posts_type_carousel ul').length > 0) {
        if (typeof $('.owl_blog_related_posts_type_carousel ul').owlCarousel === 'function') {
            $('.owl_blog_related_posts_type_carousel ul').owlCarousel({
                items: blockblog_number_posts_slider,
                loop: true,
                responsive: true,
                nav: true,
                navRewind: false,
                margin: 10,
                dots: true,

                lazyLoad: true,
                lazyFollow: true,
                lazyEffect: "fade",
            });
        }

    }

    if ($('.owl_blog_home_recents_posts_type_carousel ul').length > 0) {

        if ($('.owl_blog_home_recents_posts_type_carousel ul').length > 0) {

            if (typeof $('.owl_blog_home_recents_posts_type_carousel ul').owlCarousel === 'function') {

                $('.owl_blog_home_recents_posts_type_carousel ul').owlCarousel({
                    items: blockblog_number_home_recents_posts_slider,

                    loop: true,
                    responsive: true,
                    nav: true,
                    navRewind: false,
                    margin: 20,
                    dots: true,

                    lazyLoad: true,
                    lazyFollow: true,
                    lazyEffect: "fade",
                });

            }
        }

    }


    if ($('.owl_blog_recents_posts_type_carousel ul').length > 0) {
        
        if ($('.owl_blog_recents_posts_type_carousel ul').length > 0) {
            
            if (typeof $('.owl_blog_recents_posts_type_carousel ul').owlCarousel === 'function') {
                
                $('.owl_blog_recents_posts_type_carousel ul').owlCarousel({
                    items: 4,
                    loop: true,
                    responsive: true,
                    nav: true,
                    navRewind: false,
                    margin: 40,
                    dots: true,
                    navText: [,],

                    lazyLoad: true,
                    lazyFollow: true,
                    lazyEffect: "fade",
                    responsive: {
                        0: {
                            items: 1
                        },
                        600: {
                            items: 2
                        },
                        1000: {
                            items: 4 
                        }
                    }
                });
            }
        }

    }


    if ($('.owl_blog_cat_type_carousel ul').length > 0) {

        if ($('.owl_blog_cat_type_carousel ul').length > 0) {

            if (typeof $('.owl_blog_cat_type_carousel ul').owlCarousel === 'function') {

                $('.owl_blog_cat_type_carousel ul').owlCarousel({
                    items: 1,

                    loop: true,
                    responsive: true,
                    nav: true,
                    navRewind: false,
                    margin: 20,
                    dots: true,
                    navText: [,],

                    lazyLoad: true,
                    lazyFollow: true,
                    lazyEffect: "fade",
                });
            }
        }

    }


    if ($('.owl_blog_com_type_carousel ul').length > 0) {

        if ($('.owl_blog_com_type_carousel ul').length > 0) {

            if (typeof $('.owl_blog_com_type_carousel ul').owlCarousel === 'function') {

                $('.owl_blog_com_type_carousel ul').owlCarousel({
                    items: 1,

                    loop: true,
                    responsive: true,
                    nav: true,
                    navRewind: false,
                    margin: 20,
                    dots: true,
                    navText: [,],

                    lazyLoad: true,
                    lazyFollow: true,
                    lazyEffect: "fade",
                });
            }
        }

    }



    if ($('.owl_blog_related_posts_type_carousel_product ul').length > 0) {
        if (typeof $('.owl_blog_related_posts_type_carousel_product ul').owlCarousel === 'function') {
            $('.owl_blog_related_posts_type_carousel_product ul').owlCarousel({
                items: blockblog_number_posts_slider_product,
                loop: true,
                responsive: true,
                nav: true,
                navRewind: false,
                margin: 10,
                dots: true,

                lazyLoad: true,
                lazyFollow: true,
                lazyEffect: "fade",
            });
        }

    }



    if(typeof blockblogid_module !== 'undefined') {
        // gdpr
        var gdpr_blockblog_my = $('.gdpr-blockblog #psgdpr_consent_checkbox_' + blockblogid_module);



        if(typeof gdpr_blockblog_my !== 'undefined') {

        if (gdpr_blockblog_my.length > 0) {


            if (gdpr_blockblog_my.prop('checked') == true) {
                $('.gdpr-blockblog .gdpr_module_' + blockblogid_module + ' .psgdpr_consent_message').removeClass('error-label');
            } else {
                $('.gdpr-blockblog .gdpr_module_' + blockblogid_module + ' .psgdpr_consent_message').addClass('error-label');
            }

            $('.gdpr-blockblog  #psgdpr_consent_checkbox_' + blockblogid_module).on('click', function () {
                if (gdpr_blockblog_my.prop('checked') == true) {
                    $('.gdpr-blockblog .gdpr_module_' + blockblogid_module + ' .psgdpr_consent_message').removeClass('error-label');
                } else {
                    $('.gdpr-blockblog .gdpr_module_' + blockblogid_module + ' .psgdpr_consent_message').addClass('error-label');
                }
            });

            setTimeout(function() {
                $('.gdpr-blockblog').find('input[name="blockblogcancel"]').removeAttr('disabled'); //only for cancel button
            }, 1000);


        }

        }

        //gdpr
    }


});







function field_state_change_account(field, state, err_text)
{

    var field_label = $('label[for="'+field+'"]');
    var field_div_error = $('#'+field);

    if (state == 'success')
    {
        field_label.removeClass('error-label');
        field_div_error.removeClass('error-current-input');
    }
    else
    {
        field_label.addClass('error-label');
        field_div_error.addClass('error-current-input');
    }
    document.getElementById('error_'+field).innerHTML = err_text;

}


function confirm_action_blocblog(Text,URL){
    if(confirm(Text))
    {
        window.location.href=URL;
    }

}


function blockblog_loyalty_question(id,type,action){


    if(action == 1){
        $( ".blockblog-loyalty-question #blockblog-loyalty-tooltip"+id+type ).css( "display","block" );
    } else {
        $( ".blockblog-loyalty-question #blockblog-loyalty-tooltip"+id+type ).css( "display","none" );
    }
}

