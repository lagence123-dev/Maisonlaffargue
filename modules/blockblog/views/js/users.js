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

document.addEventListener("DOMContentLoaded", function(event) {
    $(document).ready(function(){
        $('#search').val('');
    });
});


$(document).ready(function(){

    if ($('.owl_users_type_carousel_blockblog ul').length > 0) {

        if ($('.owl_users_type_carousel_blockblog ul').length > 0) {

            if (typeof $('.owl_users_type_carousel_blockblog ul').owlCarousel === 'function') {

                $('.owl_users_type_carousel_blockblog ul').owlCarousel({
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


    if ($('.owl_users_home_type_carousel_blockblog ul').length > 0) {

        if ($('.owl_users_home_type_carousel_blockblog ul').length > 0) {

            if (typeof $('.owl_users_home_type_carousel_blockblog ul').owlCarousel === 'function') {

                $('.owl_users_home_type_carousel_blockblog ul').owlCarousel({
                    items: blockblog_number_users_home_slider,

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


});