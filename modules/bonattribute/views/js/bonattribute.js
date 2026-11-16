/*
 * 2015-2021 Bonpresta
 *
 * Bonpresta Product Attributes and Combinations
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

$(document).ready(function () {
  bonAttribute();

  prestashop.on("updateProductList", (data) => {
    bonAttribute();
  });
  prestashop.on("updateProduct", (data) => {
    bonAttribute();
  });
});

function bonAttribute() {
  $(function () {
    $(".bonattribute-dropdown > .bonattribute-caption").on(
      "click",
      function () {
        $(this).parent().toggleClass("open");
      }
    );

    $(".bonattribute-dropdown > .bonattribute-list > .bonattribute-item").on(
      "click",
      function () {
        $(
          ".bonattribute-dropdown > .bonattribute-list > .bonattribute-item"
        ).removeClass("selected");
        $(this)
          .addClass("selected")
          .parent()
          .parent()
          .removeClass("open")
          .children(".bonattribute-caption")
          .text($(this).text());
      }
    );

    $(document).on("click", function (evt) {
      if (
        $(evt.target).closest(".bonattribute-dropdown > .bonattribute-caption")
          .length === 0
      ) {
        $(".bonattribute-dropdown").removeClass("open");
      }
    });
  });

  $(".bonattribute-btn").each(function (indx, element) {
    $(this).click(function (e) {
      e.preventDefault();
      let dataIdAttr = $(this).attr("value");
      let dataPrice = $(this).attr("bon-data-price");
      let dataRegularPrice = $(this).attr("bon-data-regular-price");
      let dataQuantity = $(this).attr("data-quantity");

      $(this).parents('.thumbnail-container-inner').find('.bon_id_attr').attr("value", dataIdAttr);

      if (dataQuantity == 0) {
        $(".ajax_add_to_cart_button").prop("disabled", true);
      } else {
        $(".ajax_add_to_cart_button").prop("disabled", false);
      }

      $(".bonattribute-btn").removeClass("active");
      $(`.bonattribute-btn[value="${this.value}"]`).addClass("active");

      $(this)
        .parents(".thumbnail-container-inner")
        .find("span.price")
        .html(dataPrice);
      $(this)
        .parents(".thumbnail-container-inner")
        .find(".regular-price")
        .html(dataRegularPrice);

      let lineAttr = $(this)
        .parents(".thumbnail-container-inner")
        .find(".product-thumbnail img[data-full-size-image-url]");
      let bonIdProduct = $(this).attr("bon-data-id-product");

      data = {
        bonIdProduct,
        dataIdAttr,
      };
      $.ajax({
        type: "POST",
        url:
          bon_attribute_url +
          "?ajax=1&static_token_bon_attribute=" +
          static_token_bon_attribute,
        async: true,
        dataType: "json",
        contentType: "application/json; charset=utf-8",
        data: JSON.stringify(data),
        success: function (data) {
          lineAttr.attr("src", https  + data.img);

          $("#boncompare-popup").html(data);
        },
      }).done(function () {});
    });
  });
}
