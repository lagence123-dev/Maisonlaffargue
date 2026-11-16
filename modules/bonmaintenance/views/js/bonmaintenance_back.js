/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */

$(document).ready(function () {
  // gradient
  if ($(".form-group.content_type option[value='gradient']").is(':selected')) {
    $('.form-group.files_color_hex').removeClass('hidden');
    $('.form-group.files_color_hex').addClass('visible');
  }

  // image and video
  if (
    $(".form-group.content_type option[value='image']").is(':selected') ||
    $(".form-group.content_type option[value='video']").is(':selected')
  ) {
    $('.form-group.files_image').removeClass('hidden');
    $('.form-group.files_image').addClass('visible');
  }

  $('.form-group.content_type select').change(function () {
    if (
      $('.form-group.content_type select').val() == 'image' ||
      $('.form-group.content_type select').val() == 'video'
    ) {
      $('.form-group.files_image').removeClass('hidden');
    } else {
      $('.form-group.files_image').addClass('hidden');
    }
    if ($('.form-group.content_type select').val() == 'gradient') {
      $('.form-group.files_color_hex').removeClass('hidden');
    } else {
      $('.form-group.files_color_hex').addClass('hidden');
    }
  });
});
