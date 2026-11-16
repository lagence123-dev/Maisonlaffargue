/**
 * Cross Selling On Cart
 *
 * @author    Presta-Module.com <support@presta-module.com> - https://www.presta-module.com
 * @copyright Presta-Module 2022 - https://www.presta-module.com
 * @license   see file: LICENSE.txt
 *
 *           ____     __  __
 *          |  _ \   |  \/  |
 *          | |_) |  | |\/| |
 *          |  __/   | |  | |
 *          |_|      |_|  |_|
 */

if (typeof pm_crosssellingoncart !== 'undefined') {
    setTimeout(function() {
        pm_crosssellingoncart.prefix = '.pm-csoc .carousel';
        $(document).ready(function() {
            initCSOCCarousel(pm_crosssellingoncart.prefix);
            if (typeof(modalAjaxCart) == 'undefined' && typeof(ajaxCart) != 'undefined' && typeof(pm_reloadCartOnAdd) != 'undefined' && typeof(pm_csocLoopInterval) == 'undefined') {
                pm_csocLoopInterval = setInterval(function() {
                    pm_reloadCartOnAdd(pm_crosssellingoncart.order_page_link);
                }, 500);
            }
            if ($('body#product').size() > 0) {
                // Remove product on CSOC
                $(document).on('click', pm_crosssellingoncart.prefix + ' a.ajax_add_to_cart_button', function(e) {
                    e.preventDefault();
                    var owl = $(pm_crosssellingoncart.prefix).data('pm-csoc-owlCarousel');
                    owl.removeItem(owl.currentItem);
                    owl.reinit();

                    if ($(pm_crosssellingoncart.prefix + ' .product-box').length <= 0) {
                        $('.pm-csoc').remove();
                    }
                });
            }

            $('body').on('show.bs.modal', '#blockcart-modal', () => {
                initCSOCCarousel('#blockcart-modal ' + pm_crosssellingoncart.prefix);
            });
        });
    }, 50);

    function initCSOCCarousel(prefix) {
        $(prefix).pmCSOCOwlCarousel({
            items: 4,
            itemsCustom: false,
            itemsDesktop: 4,
            itemsDesktopSmall: false,
            itemsTablet: [768, 3],
            itemsTabletSmall: false,
            itemsMobile: [479, 1],
            slideSpeed: 200,
            paginationSpeed: 800,
            autoPlay: true,
            stopOnHover: true,
            goToFirstSpeed: 1000,
            navigation: false,
            navigationText: ["prev", "next"],
            scrollPerPage: true,
            pagination: true,
            baseClass: "pm-csoc-owl-carousel",
            theme: "pm-csoc-owl-theme",
            mouseDraggable: false,
            responsiveBaseWidth: window
        });
    }
}