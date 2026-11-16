/**
 * 2007-2020 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2020 PrestaShop SA
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

document.addEventListener('DOMContentLoaded', () => {
    const $filters = $('#offcanvasFilters');
    const $backdrop = $('#offcanvasBackdrop');

    if ($filters.length && $backdrop.length) {

        $filters.on('show.bs.collapse', function () {
            $backdrop.addClass('d-block');
            $backdrop.removeClass('d-none');
            setTimeout(() => $backdrop.addClass('show'), 10);
        });

        $filters.on('hide.bs.collapse', function () {
            $backdrop.removeClass('show');
        });

        $filters.on('hidden.bs.collapse', function () {
            $backdrop.removeClass('d-block');
            $backdrop.addClass('d-none');
        });

        $backdrop.on('click', function () {
            $filters.collapse('hide');
        });
    }

    if (typeof prestashop !== 'undefined') {
        prestashop.on('updateProductList', (data) => {
        });
    }


    // product page OwlCarousel
    if ($(".images-container-mobile").length) {
        $(".product-images").slick({
            dots: true,
            infinite: true,
            speed: 300,
            slidesToShow: 1,
        });
    }
});