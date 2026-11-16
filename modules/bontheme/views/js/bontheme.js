/*
 * 2015-2021 Bonpresta
 *
 * Bonpresta Theme
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

document.addEventListener("DOMContentLoaded", function (e) {
  prestashop.on("updateProductList", (data) => {
    updateProductListDOM(data);
  });

  function updateProductListDOM(data) {
    if ($("#category").length) {
      GridList();
    }
    selectFonts();
  }
});

$(document).ready(() => {
  BonThemePromo();
  adaptiveHeight();
  boxedBody();
  customPseudoStyles();
  changeLanguageCustom();
  openLeftMenu();
  promoCodePopup();
  selectFonts();
  clickBonTheme();
  clickMenu();
  stickyNav();
  stickyHeader();
  if ($("#category")) {
    GridList();
  }

  if (
    typeof theme_sticky_header != "undefined" &&
    theme_sticky_header == true
  ) {
    stickyHeader();
  } else {
    $(".input-sticky-header").attr("value", "off");
    $(".switch-header").removeClass("active");
    $(".Sticky-header .toggle-bg").removeClass("active");
  }

  if (
    typeof theme_enable_header != "undefined" &&
    theme_enable_header == true
  ) {
    headerChanger();
  }

  if (typeof theme_sticky_cart != "undefined" && theme_sticky_cart == true) {
    stickyCart();
  } else {
    $(".switch-cart").removeClass("active");
    $(".input-sticky-cart").attr("value", "off");
    $(".sticky-addcart .toggle-bg").removeClass("active");
  }

  if (
    typeof theme_sticky_footer != "undefined" &&
    theme_sticky_footer == true
  ) {
    stickyFooter();
  } else {
    $(".switch-footer").removeClass("active");
    $(".input-sticky-footer").attr("value", "off");
    $(".Sticky-footer .toggle-bg").removeClass("active");
  }

  $(window).resize(function () {
    stickyNav();
    stickyHeader();
  });
});
function BonThemePromo() {
  let BonLocalPromo = JSON.parse(localStorage.getItem("BonLocalPromo"));

  if (BonLocalPromo === "close") {
    $(".promo-container").removeClass("active-list");
  } else {
    $(".promo-container").addClass("active-list");
  }

  $(".close-promo-popup").on("click", function () {
    localStorage.setItem("BonLocalPromo", JSON.stringify("close"));
    $(".promo-container").removeClass("active-list");
  });
}
function stickyNav() {
  let header = document.getElementById("header");
  let headerHeight = $("#header").height();
  let sticky = header.offsetTop + headerHeight;

  if ($(window).width > 991) {
    $("#navigation").removeClass("sticky-nav");
  }
  $(window).on("scroll", function () {
    if ($(window).scrollTop() > sticky) {
      $("#navigation").addClass("sticky-nav");
    } else {
      $("#navigation").removeClass("sticky-nav");
    }
  });
}

function clickMenu() {
  jQuery(function ($) {
    $(document).mouseup(function (e) {
      var div = $("#header");
      if (!div.is(e.target) && div.has(e.target).length === 0) {
        $("#menu-icon").removeClass("active");
        $("#mobile_top_menu_wrapper").removeClass("active");
        $("#wrapper").removeClass("active");
      }
    });
  });
}

function headerChanger() {
  jQuery(function ($) {
    var div = $("#bon-header-open"),
      wrapper = $(".bon-header-menu");

    $(div).on("click", function () {
      $(this).toggleClass("active");
      $(wrapper).toggleClass("active");
    });

    $(document).on("click", function (event) {
      if (
        !$(event.target).closest(div).length &&
        !$(event.target).closest(wrapper).length
      ) {
        $(div).removeClass("active");
        $(wrapper).removeClass("active");
      }
    });

    $(".header-type").on("click", function () {
      let headerType = "header-" + $(this).data("type");
      $("body").removeClass(removeHeaderClass).addClass(headerType);
    });
  });
}

function removeHeaderClass(_, className) {
  return (className.match(/\bheader-\S+/g) || []).join(" ");
}

function clickBonTheme() {
  jQuery(function ($) {
    $(document).mouseup(function (e) {
      var div = $(".bon-custom-menu");
      if (!div.is(e.target) && div.has(e.target).length === 0) {
        $("#custom-menu-open").removeClass("custom-button-active");
        $(".bon-custom-menu").removeClass("custom-menu-active");
      }
    });
  });
}

function promoCodePopup() {
  let promoList = $(".promo-list");
  if (promoList.length) {
    let listItems = $("li", promoList);
    if (listItems.length) {
      $(".close-promo-popup").on("click", function () {
        $(".promo-container").removeClass("active-list");
      });
    }
  }
}

function stickyHeader() {
  let prevScrollPos = window.scrollY;
  const header = document.getElementById("header");
  const headerHeight = $(header).height();
  const sticky = header.offsetTop + headerHeight;

  $(window).on("scroll", function () {
    if ($(".input-sticky-header").attr("value") === "on") {
      const currentScrollPos = window.scrollY;

      if ($(window).scrollTop() > 0) {
        $(header).addClass("sticky-head");
        if (
          currentScrollPos < prevScrollPos ||
          $(".bonmm-mobile-button").hasClass("active") ||
          $("#menu-icon").hasClass("active")
        ) {
          header.style.transform = "translateY(0)";
        } else {
          header.style.transform = "translateY(-100%)";
        }
        prevScrollPos = currentScrollPos;
      } else {
        $(header).removeClass("sticky-head");
        header.style.transform = "translateY(0)";
      }
    } else {
      $(header).removeClass("sticky-head");
      header.style.transform = "translateY(0)";
    }
  });
}

$(window).resize(stickyCart);
$(window).resize(stickyFooter);

function stickyCart() {
  let skrollHeight = 1000;
  $(window).on("scroll", function () {
    if (
      $(window).width() >= 1300 &&
      $(".input-sticky-cart").attr("value") === "on"
    ) {
      if ($(window).scrollTop() > skrollHeight) {
        $("#product .product-information .product-actions").attr(
          "id",
          "bon-stick-cart"
        );
        $("#product #footer").css("padding-bottom", "100px");
      } else {
        $("#product .product-information .product-actions").attr("id", "");
        $("#product #footer").css("padding-bottom", "0");
      }
    } else {
      $("#product .product-information .product-actions").attr("id", "");
      $("#product #footer").css("padding-bottom", "0");
    }
  });
}

function openLeftMenu() {
  let customMenu = $(".bon-custom-menu");
  let customMenuButton = $("#custom-menu-open");
  $(customMenuButton).on("click", function () {
    if ($(customMenuButton).hasClass("custom-button-active")) {
      $(customMenuButton).removeClass("custom-button-active");
      $(customMenu).removeClass("custom-menu-active");
    } else {
      $(customMenuButton).addClass("custom-button-active");
      $(customMenu).addClass("custom-menu-active");
    }
  });
}

function boxedBody() {
  if ($(window).width() > 1600) {
    $(".input-boxed").on("click", function () {
      if ($(".switch-boxed").hasClass("active")) {
        $(".switch-boxed").removeClass("active");
        $(".input-boxed").parent().removeClass("active");
        $(".toggle-bg .input-boxed").attr("value", "off");
      } else {
        $(".switch-boxed").addClass("active");
        $(".input-boxed").parent().addClass("active");
        $(".toggle-bg .input-boxed").attr("value", "on");
      }
      if ($(".input-boxed").attr("value") === "on") {
        $("#footer").addClass("boxed");
        $("main").addClass("boxed");
        $("#bonbanners").addClass("boxed-banners");
        $("body").addClass("boxed-body");
        $(".footer-container").css("margin-top", "0px");
        $(".box-bonslick").addClass("slick-boxed");
        $("#bon-stick-cart").addClass("boxed");
        $(".bon-shipping").addClass("shipping-boxed");
        $(".product-container").addClass("boxed");
      } else {
        $("main").removeClass("boxed");
        $("#footer").removeClass("boxed");
        $(".footer-container").css("margin-top", "20px");
        $("body").removeClass("boxed-body");
        $("#bonbanners").removeClass("boxed-banners");
        $(".box-bonslick").removeClass("slick-boxed");
        $("#bon-stick-cart").removeClass("boxed");
        $(".bon-shipping").removeClass("shipping-boxed");
        $(".product-container").removeClass("boxed");
      }
    });
  } else {
    $(".boxed-setting").css("display", "none");
  }
}

$(".input-sticky-header").on("click", function () {
  if ($(".switch-header").hasClass("active")) {
    $(".switch-header").removeClass("active");
    $(".input-sticky-header").parent().removeClass("active");
    $(".input-sticky-header").attr("value", "off");
  } else {
    $(".switch-header").addClass("active");
    $(".input-sticky-header").parent().addClass("active");
    $(".input-sticky-header").attr("value", "on");
    stickyHeader();
  }
});

$(".input-sticky-cart").on("click", function () {
  if ($(".switch-cart").hasClass("active")) {
    $(".switch-cart").removeClass("active");
    $(".input-sticky-cart").parent().removeClass("active");
    $(".input-sticky-cart").attr("value", "off");
  } else {
    $(".switch-cart").addClass("active");
    $(".input-sticky-cart").parent().addClass("active");
    $(".input-sticky-cart").attr("value", "on");
    stickyCart();
  }
});

$(".input-sticky-footer").on("click", function () {
  if ($(".switch-footer").hasClass("active")) {
    $(".switch-footer").removeClass("active");
    $(".input-sticky-footer").parent().removeClass("active");
    $(".input-sticky-footer").attr("value", "off");
    stickyFooter();
  } else {
    $(".switch-footer").addClass("active");
    $(".input-sticky-footer").parent().addClass("active");
    $(".input-sticky-footer").attr("value", "on");
    stickyFooter();
  }
});

function stickyFooter() {
  let windowWidth = parseInt($(window).width());
  let footerResponsiveHeight = parseInt($("#footer").outerHeight(true));
  let footer = $("#footer");
  let mainContainer = $("main");
  let nav = navigator.userAgent;
  let documentHeight = parseInt($(document).height());

  if (
    windowWidth >= 768 &&
    $(".input-sticky-footer").attr("value") === "on" &&
    documentHeight > 1300
  ) {
    if (navigator.userAgent.search("Chrome") >= 0 && !nav.match(/Edge/)) {
      $(footer).addClass("sticky-footer");
      $(mainContainer).css("margin-bottom", "0");
      // $(mainContainer).css('margin-bottom', footerResponsiveHeight + 'px');
    } else {
      $(footer).removeClass("sticky-footer");
      mainContainer.css("margin-bottom", "0");
      mainContainer.css("padding-bottom", "0");
    }
  } else {
    $(footer).removeClass("sticky-footer");
    mainContainer.css("margin-bottom", "0");
    mainContainer.css("padding-bottom", "0");
    $(".input-sticky-footer").attr("value", "off");
  }
}

function adaptiveHeight() {
  let mainContainer = $("main");
  let documentHeight = parseInt($(document).height());

  if (documentHeight < 1300) {
    $("#footer").removeClass("sticky-footer").css("z-index", "1");
    $(mainContainer).css("margin-bottom", "0");
  }

  $(window).on("scroll", function () {
    if (documentHeight < 1300) {
      $("#footer").removeClass("sticky-footer").css("z-index", "1");
      $(mainContainer).css("margin-bottom", "0");
    }
  });

  if (navigator.userAgent.search("Firefox") >= 0) {
    if (documentHeight < 1300) {
      $("#footer").removeClass("sticky-footer").css("z-index", "1");
      $(mainContainer).css("padding-bottom", "0");
      $(mainContainer).css("margin-bottom", "0");
    }

    $(window).on("scroll", function () {
      if (documentHeight < 1300) {
        $("#footer").removeClass("sticky-footer").css("z-index", "1");
        $(mainContainer).css("padding-bottom", "0");
        $(mainContainer).css("margin-bottom", "0");
      }
    });
  }
}

function selectFonts() {
  let body = $("body");
  body.addClass(theme_fonts);
  if (body.hasClass(theme_fonts)) {
    $(this).removeClass(theme_fonts);
  }
}

function changeLanguageCustom() {
  $(".bon-select-form").on("change", function () {
    let languageValue = $(this).val();
    let body = $("body");
    switch (languageValue) {
      case "Lato":
        $(body).addClass("Lato");
        $(body).removeClass(
          "Raleway OpenSans Poppins Roboto Oswald Ubuntu Playfair Lora Indie Hind"
        );
        break;
      case "Raleway":
        $(body).addClass("Raleway");
        $(body).removeClass(
          "OpenSans Poppins Roboto Oswald Ubuntu Playfair Lora Indie Hind Lato"
        );
        break;
      case "OpenSans":
        $(body).addClass("OpenSans");
        $(body).removeClass(
          "Raleway  Poppins Roboto Oswald Ubuntu Playfair Lora Indie Hind Lato"
        );
        break;
      case "Roboto":
        $(body).addClass("Roboto");
        $(body).removeClass(
          "Raleway  Poppins OpenSans Oswald Ubuntu Playfair Lora Indie Hind Lato"
        );
        break;
      case "Oswald":
        $(body).addClass("Oswald");
        $(body).removeClass(
          "Raleway  Poppins OpenSans Roboto Ubuntu Playfair Lora Indie Hind Lato"
        );
        break;
      case "Ubuntu":
        $(body).addClass("Ubuntu");
        $(body).removeClass(
          "Raleway  Poppins OpenSans Roboto Oswald Playfair Lora Indie Hind Lato"
        );
        break;
      case "Playfair":
        $(body).addClass("Playfair");
        $(body).removeClass(
          "Raleway  Poppins OpenSans Roboto Oswald Ubuntu Lora Indie Hind Lato"
        );
        break;
      case "Lora":
        $(body).addClass("Lora");
        $(body).removeClass(
          "Raleway  Poppins OpenSans Roboto Oswald Ubuntu Playfair Indie Hind Lato"
        );
        break;
      case "Indie":
        $(body).addClass("Indie");
        $(body).removeClass(
          "Raleway  Poppins OpenSans Roboto Oswald Ubuntu Playfair Lora Hind Lato"
        );
        break;
      case "Hind":
        $(body).addClass("Hind");
        $(body).removeClass(
          "Raleway  Poppins OpenSans Roboto Oswald Ubuntu Playfair Lora Indie Lato"
        );
        break;
      default:
        $("body").addClass("Poppins");
        $(body).removeClass(
          "Raleway Hind OpenSans Roboto Oswald Ubuntu Playfair Lora Indie Lato"
        );
        break;
    }
  });
}

function customPseudoStyles() {
  window.addRule = function (selector, styles, sheet) {
    styles = (function (styles) {
      if (typeof styles === "string") return styles;
      var clone = "";
      for (var prop in styles) {
        if (styles.hasOwnProperty(prop)) {
          var val = styles[prop];
          prop = prop.replace(/([A-Z])/g, "-$1").toLowerCase();
          clone +=
            prop + ":" + (prop === "content" ? '"' + val + '"' : val) + "; ";
        }
      }
      return clone;
    })(styles);
    sheet = sheet || document.styleSheets[document.styleSheets.length - 1];

    if (sheet.insertRule)
      sheet.insertRule(selector + " {" + styles + "}", sheet.cssRules.length);
    else if (sheet.addRule) sheet.addRule(selector, styles);

    return this;
  };

  if ($)
    $.fn.addRule = function (styles, sheet) {
      addRule(this.selector, styles, sheet);
      return this;
    };
}

function MyStyleColor(color) {
  $(" #productCommentsBlock .pull-right .open-comment-form").addRule({
    borderColor: color.rgbaString,
  });
  $(
    " #header .header-top .position-static #_desktop_setting-header i.active"
  ).addRule({
    color: color.rgbaString,
  });
  $(
    "#bonwishlist .wishlist-summary-product-name .product-title:hover span"
  ).addRule({
    color: color.rgbaString,
  });

  $("#bonwishlist .wishlist-tooltip:hover i").addRule({
    color: color.rgbaString,
  });

  $("#bonwishlist .wishlist_add_to_cart_button:hover i").addRule({
    color: color.rgbaString,
  });

  $("li.product-flag.new:after").addRule({
    borderColor: color.rgbaString,
    borderRightColor: "transparent",
    borderBottomColor: "transparent",
  });

  $("#product li.product-flag.new:after").addRule({
    borderColor: color.rgbaString,
    borderRightColor: "transparent",
    borderBottomColor: "transparent",
  });

  $("#_desktop_top_menu ul[data-depth='0']>li>a:after").addRule({
    backgroundColor: color.rgbaString,
  });

  $(".tabs .nav-tabs .nav-item .nav-link:after").addRule({
    backgroundColor: color.rgbaString,
  });

  $(".footer-container .links li a:hover:before").addRule({
    color: color.rgbaString,
  });

  $(".custom-radio input[type='radio'] + span:before").addRule({
    backgroundColor: color.rgbaString,
  });

  $(".products-sort-order .select-title:after").addRule({
    color: color.rgbaString,
  });

  $("#video-container #controls .play:hover:before").addRule({
    color: color.rgbaString,
  });

  $("#video-container #controls .pause:hover:before").addRule({
    color: color.rgbaString,
  });

  $("#video-container #controls .mute:hover:before").addRule({
    color: color.rgbaString,
  });

  $("#video-container #controls .unmute:hover:before").addRule({
    color: color.rgbaString,
  });

  $("#bonslick .slick-prev:hover:before").addRule({
    color: color.rgbaString,
  });

  $("#bonslick .slick-next:hover:before").addRule({
    color: color.rgbaString,
  });

  $(
    "#main .images-container .js-qv-mask .slick-slider .slick-arrow.slick-next:hover:before"
  ).addRule({
    color: color.rgbaString,
  });

  $(
    "#main .images-container .js-qv-mask .slick-slider .slick-arrow.slick-prev:hover:before"
  ).addRule({
    color: color.rgbaString,
  });

  $(
    ".product-add-to-cart .product-quantity .bon-product-popup .bon-product-delivery a:hover:before"
  ).addRule({
    color: color.rgbaString,
  });

  $(
    ".product-add-to-cart .product-quantity .bon-product-popup .bon-product-size a:hover:before"
  ).addRule({
    color: color.rgbaString,
  });

  $(
    ".product-add-to-cart .product-quantity .bon-product-popup .bon-review-inner a:hover:before"
  ).addRule({
    color: color.rgbaString,
  });

  $(".social-sharing ul li a:hover:before").addRule({
    color: color.rgbaString,
  });

  $(".product-quantity .bon-product-popup .title-popup-1:hover:before").addRule(
    {
      color: color.rgbaString,
    }
  );

  $(".product-quantity .bon-product-popup .title-popup-2:hover:before").addRule(
    {
      color: color.rgbaString,
    }
  );

  $(
    ".product-add-to-cart .product-quantity .bon-review-inner a:hover:before"
  ).addRule({
    color: color.rgbaString,
  });

  $(
    ".product-add-to-cart .product-quantity .bon-product-popup .bon-review-inner a:hover:before," +
      ".product-add-to-cart .product-quantity .bon-product-popup .bon-product-delivery a:hover:before," +
      ".product-add-to-cart .product-quantity .bon-product-popup .bon-product-size a:hover:before"
  ).addRule({
    color: color.rgbaString,
  });

  if (navigator.userAgent.search("Chrome") >= 0) {
    $("::-webkit-scrollbar-thumb:hover").addRule({
      backgroundColor: color.rgbaString,
    });
  }

  $(
    ".btn-primary, #bonslick .slick-slide .box-bonslick span, #bonbanners a .banner-inner span"
  ).css({
    background: "none",
    "border-color": color.rgbaString,
    "box-shadow": "none",
    backgroundColor: color.rgbaString,
  });

  $(
    "#header .blockcart .cart-products-count, #write-review-anchor, #bonwishlist .wishlist-count, #bonslick .box-bonslick span" +
      ", li.product-flag.new, .meshim_widget_components_chatButton_Button .button_bar, body .bon-shipping" +
      ", .btn-primary, .custom-checkbox input[type=checkbox]+span .checkbox-checked, .bonpromotion-countdown-btn, .bonsearch .bonsearch_btn, .product-actions .add-to-cart, .toggle-bg.active," +
      ".product-add-to-cart .product-quantity .bon-stock-countdown .bon-stock-countdown-range .bon-stock-countdown-progress"
  ).css("background", color.rgbaString);

  $(
    "#_desktop_top_menu .top-menu .nav-arrows i, #product-availability .product-available, .pagination .current a, .product-page-right .product-price .current-price, #_desktop_top_menu > .top-menu> li.sfHover > a," +
      "#main .product-information .product-actions #group_1 .input-container label span.check,#_desktop_search_widget .bonsearch_button.active, #header .header-top .position-static #_desktop_setting-header i.active" +
      ", .quickview .modal-content .modal-body .product-price .current-price,.product-container .product-list .product-item .item-description .product-item-name, #bonwishlist .wishlist_add_to_cart_button:hover i"
  ).css("color", color.rgbaString);

  $(
    '#header .top-menu a[data-depth="0"], #header .header-top .position-static #_desktop_setting-header i, .bonsearch,' +
      "#header .header-top .position-static #_desktop_user_info i, #header .header-top .position-static #_desktop_cart .blockcart i," +
      ".bonthumbnails li a, .footer-container .links li a, #wrapper .breadcrumb li a, .pagination a:not(.previous):not(.next), .pagination .next, .pagination .previous, .featured-products .product-title a," +
      '.product-accessories .product-title a, .product-miniature .product-title a, .footer-container-bottom a, #search_filters .facet .facet-label a, #_desktop_top_menu .sub-menu ul[data-depth="1"]>li a' +
      ', #_desktop_top_menu .sub-menu ul[data-depth="2"]>li a, .footer-container .product-container .product-list .product-item .item-description .product-item-name' +
      ", #header #_desktop_currency_selector .currency-selector ul li a, #back-to-top, .bonsearch #search_popup .wrap_item .product_image h5, .bon_manufacture_list h4 a, #bon_manufacturers_block .owl-nav .owl-prev" +
      ",#bon_manufacturers_block .owl-nav .owl-next, #bonwishlist .wishlist-tooltip i, .product-add-to-cart .product-quantity .bon-product-popup .bon-product-delivery a,.product-add-to-cart .product-quantity .bon-product-popup .bon-product-size a," +
      ".product-add-to-cart .product-quantity .bon-product-popup .bon-review-inner a, .comments_note a span, .bon-newsletter .bon-newsletter-close > i," +
      ".product-add-to-cart .product-quantity .bon-product-popup .bon-product-delivery a, #main .product-information .product-actions #group_1 .input-container label:hover span.radio-label," +
      ".product-add-to-cart .product-quantity .bon-product-popup .bon-product-size a, .product-add-to-cart .product-quantity .bon-product-popup .bon-product-size a," +
      ".product-quantity .bon-product-popup .title-popup-1, .product-quantity .bon-product-popup .title-popup-2, .product-add-to-cart .product-quantity .bon-review-inner a, .product-container .product-list .product-item .item-description .product-item-name"
  ).hover(function () {
    $(this).css("color", color.rgbaString).addClass("color-bon");
    $(".active-color-bon")
      .not($(this))
      .removeClass("color-bon")
      .css("color", "");
    $(this).on("mouseleave", function () {
      $(this).css("color", "");
    });
  });

  $(
    "#main .product-information .product-actions #group_1 .input-container label span.check"
  ).css("border-color", color.rgbaString);

  $(
    ".btn-primary, .featured-products .thumbnail-container .ajax_add_to_cart_button"
  ).hover(function () {
    $(this).css("border-color", color.rgbaString);
    $(this).css("backgroundColor", color.rgbaString);
  });

  $("#productCommentsBlock .pull-right .open-comment-form").hover(function () {
    $(this).css("border-color", color.rgbaString);
    $(this).css("backgroundColor", color.rgbaString);
    $(this).css("color", "#ffffff");
  });

  $(
    ".bonthumbnails li a, #main .images-container .js-qv-mask .slick-slider .slick-slide,#main .images-container .js-qv-mask .slick-slider .slick-slide.selected"
  ).hover(function () {
    $(this)
      .css("box-shadow", "inset 0 0 0 2px " + color.rgbaString)
      .addClass("active-hover-bon");
    $(".active-hover-bon")
      .not($(this))
      .removeClass("active-hover-bon")
      .css("box-shadow", "");
  });

  $(".bonthumbnails li.active").css(
    "box-shadow",
    "inset 0 0 0 2px " + color.rgbaString
  );
}

// Grid list

function GridList() {
  animateGrid();

  if ($("#category").length) {
    var product_col = 4;
    gridResponse();

    function gridResponse() {
      if ($(window).width() > 1024) {
        $(".products-grid").addClass("active");

        prestashop.on("updateProductList", () => {
          $(".products-grid").addClass("active");
          GridCss();
          GridAddClass();
        });

        function GridCss() {
          $(".products-grid.active").attr(
            "style",
            "--product-col:" + product_col
          );
        }

        function GridAddClass() {
          var product_col = 4;
          if (product_col == 1) {
            localStorage.setItem("class", JSON.stringify("product-one"));
            $("#js-product-list article").addClass("product-one");
          } else {
            $("#js-product-list article").removeClass("product-one");
          }
          if (product_col == 2) {
            localStorage.setItem("class", JSON.stringify("product-two"));
            $("#js-product-list article").addClass("product-two");
          } else {
            $("#js-product-list article").removeClass("product-two");
          }
          if (product_col == 3) {
            localStorage.setItem("class", JSON.stringify("product-three"));
            $("#js-product-list article").addClass("product-three");
          } else {
            $("#js-product-list article").removeClass("product-three");
          }
          if (product_col == 4) {
            localStorage.setItem("class", JSON.stringify("product-four"));
            $("#js-product-list article").addClass("product-four");
          } else {
            $("#js-product-list article").removeClass("product-four");
          }
        }

        GridCss();
        $(".buttons-grid")
          .find("button[data-grid=" + product_col + "]")
          .addClass("--active");
        $(".products-grid.active").css("--product-col", product_col);

        $(".buttons-grid").on("click", "button[data-grid]", function () {
          animateGrid();
          if (!$(this).hasClass("--active")) {
            product_col = $(this).attr("data-grid");
            $(".buttons-grid").find("button.--active").removeClass("--active");
            $(this).addClass("--active");
            GridCss();
            localStorage.setItem("ProductCol", JSON.stringify(product_col));
          }
          GridAddClass();
        });
        GridAddClass();
      }
    }

    $(window).resize(function () {
      gridResponse();
      if ($(window).width() <= 1024) {
        $("#js-product-list article").removeClass("product-one");
        $(".products-grid.active").removeClass("active");
      } else if ($(window).width() > 1024) {
        gridResponse();
      }
    });
  }

  function animateGrid() {
    let $window = $(window);
    $(".revealOnScroll").addClass("animated");

    $window.on("scroll", gridAnimate);

    function gridAnimate() {
      let scrolled = $window.scrollTop(),
        win_height_padded = $window.height() * 1.1;
      $(".gridAnimate.revealOnScroll:not(.animated)").each(function () {
        let $this = $(this),
          offsetTop = $this.offset().top;
        if (scrolled + win_height_padded > offsetTop) {
          if ($this.data("timeout")) {
            window.setTimeout(function () {
              $this.addClass("animated " + $this.data("animation"));
            }, 300);
          } else {
            $this.addClass("animated " + $this.data("animation"));
          }
        }
      });
      $(".gridAnimate.revealOnScroll.animated").each(function () {
        let $this = $(this),
          offsetTop = $this.offset().top;
        if (scrolled + win_height_padded < offsetTop) {
          $(this).removeClass(
            "animated fadeInUp zoomIn fadeInLeft rollIn rotateInDownRight wobble flash pulse fadeInDown fadeInRight rotateIn rotateInUpLeft tada shake fadeInLeftBig lightSpeedIn slideInUp  flipInY hinge fadeInLeftBig flip rotateInDownLeft  rotateInUpRight slideInLeft slideInRight  flipInX "
          );
        }
      });
    }
  }

  let count8 = 1;

  $("#category .product-miniature").each(function () {
    $(this).attr("data-timeout", count8 * 90);
    count8++;
  });
}
