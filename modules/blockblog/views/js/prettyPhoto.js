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

function init_gallery_blockblog() {



        // left column
        if ($('.gallery-block-blockblog').length > 0) {
            $("a[rel^='prettyPhotoGalleryBlockLeft']").prettyPhoto(
                {
                    animation_speed: 'normal',
                    theme: slider_effect_blockblog,
                    slideshow: gallery_speed_blockblog,
                    autoplay_slideshow: gallery_autoplay_blockblog,
                    social_tools: '',
                    deeplinking: false
                }
            );
        }


        // right column
        if ($('.gallery-block-blockblog').length > 0) {
            $("a[rel^='prettyPhotoGalleryBlockRight']").prettyPhoto(
                {
                    animation_speed: 'normal',
                    theme: slider_effect_blockblog,
                    slideshow: gallery_speed_blockblog,
                    autoplay_slideshow: gallery_autoplay_blockblog,
                    social_tools: '',
                    deeplinking: false
                }
            );
        }


        // footer column
        if ($('.gallery-block-blockblog').length > 0) {
            $("a[rel^='prettyPhotoGalleryBlockFooter']").prettyPhoto(
                {
                    animation_speed: 'normal',
                    theme: slider_effect_blockblog,
                    slideshow: gallery_speed_blockblog,
                    autoplay_slideshow: gallery_autoplay_blockblog,
                    social_tools: '',
                    deeplinking: false
                }
            );
        }


        // home
        if ($('.gallery-block-blockblog-home').length > 0) {
            $("a[rel^='prettyPhotoGalleryBlockHome']").prettyPhoto(
                {
                    animation_speed: 'normal',
                    theme: slider_effect_blockblog,
                    slideshow: gallery_speed_blockblog,
                    autoplay_slideshow: gallery_autoplay_blockblog,
                    social_tools: '',
                    deeplinking: false
                }
            );
        }


    // gallery page with all images
        if ($('.gallery_item').length > 0) {

            $("a[rel^='prettyPhotoGalleryPage']").prettyPhoto(
                {
                    animation_speed: 'normal',
                    theme: slider_effect_blockblog,
                    slideshow: gallery_speed_blockblog,
                    autoplay_slideshow: ($('.gallery_list .gallery_item').length > 1 ? gallery_autoplay_blockblog : false),
                    social_tools: '',
                    deeplinking: false
                }
            );
        }
        // gallery page with all images



}



document.addEventListener("DOMContentLoaded", function(event) {
    $(document).ready(function(){
        init_gallery_blockblog();
    });
});