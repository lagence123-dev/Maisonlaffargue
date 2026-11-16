<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.txt
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to a newer
 * versions in the future. If you wish to customize this module for your
 * needs please refer to CustomizationPolicy.txt file inside our module for more information.
 *
 * @author Webkul IN
 * @copyright Since 2010 Webkul
 * @license https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class WkGiftCardGiftCardListModuleFrontController extends ModuleFrontController
{
    public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();
        $breadcrumb['links'][] = [
            'title' => $this->module->l('Gift cards', 'GiftCard'),
            'url' => '',
        ];

        return $breadcrumb;
    }

    public function initContent()
    {
        parent::initContent();

        if (Tools::getIsset('action') && Tools::getValue('action') == 'filterGiftCardByTag') {
            return;
        }

        if (!Configuration::get('WK_GIFT_CARD_MENU')) {
            Tools::redirect($this->context->link->getPageLink('pagenotfound'));
        }

        $string = trim(Tools::getValue('q'));
        $type = trim(Tools::getValue('type'));
        $this->context->smarty->assign(
            [
                'search_string' => $string,
                'search_type' => $type,
            ]
        );
        $p = Tools::getValue('p');
        $n = Configuration::get('PS_PRODUCTS_PER_PAGE');
        // default page number
        if (!$p) {
            $p = 1;
        }

        $orderBy = 'id_product'; // Display by default new product first
        $orderWay = 'desc'; // Display by default new product first
        if (Tools::getValue('orderby')) {
            $orderBy = Tools::getValue('orderby');
        }
        if (Tools::getValue('orderway')) {
            $orderWay = Tools::getValue('orderway');
        }
        if ($orderBy == 'name' && $orderWay == 'asc') {
            $order = 1;
        } elseif ($orderBy == 'name' && $orderWay == 'desc') {
            $order = 2;
        } elseif ($orderBy == 'price' && $orderWay == 'asc') {
            $order = 3;
        } elseif ($orderBy == 'price' && $orderWay == 'desc') {
            $order = 4;
        } else {
            $order = 0;
        }

        $giftCardProducts = WkGiftCardObj::getGiftCardProducts(
            $string,
            $type,
            $this->context->language->id,
            $orderBy,
            $orderWay
        );

        $appliedProducts = WkGiftCardObj::getProductsPresenter($giftCardProducts);
        $appliedGiftCardsProduct = $this->filterProductsByPage($appliedProducts, $p, $n);
        if ($appliedProducts) {
            $this->pagination(count($appliedProducts));
        }

        $bannerImgPath = _MODULE_DIR_ . $this->module->name . '/views/img/banner/' . Configuration::get('WK_GIFT_CARD_BANNER_IMAGE', $this->context->shop->id);
        $bannerImgFullPath = false;
        if (Configuration::get('WK_GIFT_CARD_BANNER_IMAGE', $this->context->shop->id)) {
            $bannerImgFullPath = _PS_MODULE_DIR_ . $this->module->name . '/views/img/banner/' . Configuration::get('WK_GIFT_CARD_BANNER_IMAGE', $this->context->shop->id);
        }

        $this->context->smarty->assign(
            [
                'banner_image_url_exist' => $bannerImgFullPath ? file_exists($bannerImgFullPath) : false,
                'banner_image_url' => $bannerImgPath,
            ]
        );

        if ($giftCardProducts) {
            $idLang = $this->context->language->id;
            $allAvailableTags = [];
            foreach ($giftCardProducts as $giftCardProduct) {
                $productTags = Tag::getProductTags($giftCardProduct['id_product']);
                if ($productTags) {
                    $allAvailableTags = array_merge($allAvailableTags, $productTags[$idLang]);
                }
            }

            $allAvailableTags = array_unique($allAvailableTags);
            $this->context->smarty->assign(
                [
                    'products' => $appliedGiftCardsProduct,
                    'total_items' => count($giftCardProducts),
                    'order_selected' => $order,
                    'available_tags' => $allAvailableTags,
                    'staticPrices' => $this->getStaticProductPrice(),
                    'wkGiftCartListLink' => $this->context->link->getModuleLink('wkgiftcard', 'giftcardlist'),
                ]
            );
        }

        $this->setTemplate('module:wkgiftcard/views/templates/front/gift_card_list.tpl');
    }

    public function filterProductsByPage($shopProduct, $p, $n)
    {
        $result = [];
        if ($shopProduct) {
            $start = ($p - 1) * $n;
            $end = $start + $n;
            for ($i = $start; $i < $end; ++$i) {
                if (array_key_exists($i, $shopProduct)) {
                    $result[] = $shopProduct[$i];
                }
            }
        }

        return $result;
    }

    public function pagination($totalProducts = null)
    {
        $this->context = Context::getContext();

        // Retrieve the default number of products per page and the other available selections
        $defaultProductsPerPage = max(1, (int) Configuration::get('PS_PRODUCTS_PER_PAGE'));
        $nArray = [$defaultProductsPerPage, $defaultProductsPerPage * 2, $defaultProductsPerPage * 5];

        if ((int) Tools::getValue('n') && (int) $totalProducts > 0) {
            $nArray[] = $totalProducts;
        }

        // Retrieve the current number of products per page
        // (either the default, the GET parameter or the one in the cookie)

        $this->n = $defaultProductsPerPage;

        if (
            isset($this->context->cookie->nb_item_per_page)
            && in_array($this->context->cookie->nb_item_per_page, $nArray)
        ) {
            $this->n = (int) $this->context->cookie->nb_item_per_page;
        }

        if ((int) Tools::getValue('n') && in_array((int) Tools::getValue('n'), $nArray)) {
            $this->n = (int) Tools::getValue('n');
        }

        // Retrieve the page number (either the GET parameter or the first page)
        $this->p = (int) Tools::getValue('p', 1);

        // If the parameter is not correct then redirect
        // (do not merge with the previous line,
        // the redirect is required in order to avoid duplicate content)

        if (Tools::getIsset('action') && Tools::getValue('action') != 'filterGiftCardByTag') {
            if (!is_numeric($this->p) || $this->p < 1) {
                Tools::redirect($this->context->link->getPaginationLink(false, false, $this->n, false, 1, false));
            }
        }

        // Remove the page parameter in order to get a clean URL for the pagination template
        $currentUrl = preg_replace('/(?:(\?)|&amp;)p=\d+/', '$1', Tools::htmlentitiesUTF8($_SERVER['REQUEST_URI']));

        if ($this->n != $defaultProductsPerPage || isset($this->context->cookie->nb_item_per_page)) {
            $this->context->cookie->nb_item_per_page = $this->n;
        }

        $pagesNb = ceil($totalProducts / (int) $this->n);
        if (Tools::getIsset('action') && Tools::getValue('action') != 'filterGiftCardByTag') {
            if ($this->p > $pagesNb && $totalProducts != 0) {
                Tools::redirect($this->context->link->getPaginationLink(false, false, $this->n, false, $pagesNb, false));
            }
        }
        $range = 2; /* how many pages around page selected */
        $start = (int) ($this->p - $range);
        if ($start < 1) {
            $start = 1;
        }

        $stop = (int) ($this->p + $range);
        if ($stop > $pagesNb) {
            $stop = (int) $pagesNb;
        }

        if (Tools::getIsset('action') && Tools::getValue('action') == 'filterGiftCardByTag') {
            if (!$this->p) {
                $this->p = 1;
            }
        }

        $this->context->smarty->assign(
            [
                'nb_products' => $totalProducts,
                'products_per_page' => $this->n,
                'pages_nb' => $pagesNb,
                'p' => $this->p,
                'n' => $this->n,
                'nArray' => $nArray,
                'range' => $range,
                'start' => $start,
                'stop' => $stop,
                'current_url' => $currentUrl,
            ]
        );
    }

    public function setMedia()
    {
        $this->addCSS(_MODULE_DIR_ . 'wkgiftcard/views/css/gift_card_list.css');

        return parent::setMedia();
    }

    public function displayAjaxFilterGiftCardByTag()
    {
        if (!$this->isTokenValid()) {
            return false;
        }
        $context = Context::getContext();
        $tagName = Tools::getValue('tag');

        if ($tagName) {
            $objTag = new Tag(null, $tagName, $this->context->language->id);
            if (Validate::isLoadedObject($objTag)) {
                $string = trim(Tools::getValue('q'));
                $type = trim(Tools::getValue('type'));
                $p = Tools::getValue('p');
                $n = Configuration::get('PS_PRODUCTS_PER_PAGE');
                $this->context->smarty->assign(
                    [
                        'search_string' => $string,
                        'search_type' => $type,
                    ]
                );
                // default page number
                if (!$p || $p == 'false') {
                    $p = 1;
                }

                $orderBy = 'id_product'; // Display by default new product first
                $orderWay = 'desc'; // Display by default new product first
                if (Tools::getValue('orderby')) {
                    $orderBy = Tools::getValue('orderby');
                }
                if (Tools::getValue('orderway')) {
                    $orderWay = Tools::getValue('orderway');
                }
                if ($orderBy == 'name' && $orderWay == 'asc') {
                    $order = 1;
                } elseif ($orderBy == 'name' && $orderWay == 'desc') {
                    $order = 2;
                } elseif ($orderBy == 'price' && $orderWay == 'asc') {
                    $order = 3;
                } elseif ($orderBy == 'price' && $orderWay == 'desc') {
                    $order = 4;
                } else {
                    $order = 0;
                }

                $giftCardProducts = WkGiftCardObj::getGiftCardProducts(
                    $string,
                    $type,
                    $this->context->language->id,
                    $orderBy,
                    $orderWay
                );

                $allTagRelatedProducts = $objTag->getProducts();

                $filteredProducts = [];
                if ($giftCardProducts) {
                    foreach ($allTagRelatedProducts as $tagRelatedProd) {
                        if (in_array($tagRelatedProd['id_product'], array_column($giftCardProducts, 'id_product'))) {
                            $filteredProducts[] = [
                                'id_product' => $tagRelatedProd['id_product'],
                            ];
                        }
                    }
                } else {
                    $filteredProducts = $allTagRelatedProducts;
                    if ($filteredProducts && is_array($filteredProducts)) {
                        $objWkGiftCardObj = new WkGiftCardObj();
                        $giftCardProduct = [];
                        foreach ($filteredProducts as $prod) {
                            if ($objWkGiftCardObj->getActiveVoucherIdByProductId($prod['id_product'])) {
                                $giftCardProduct[] = $prod;
                            }
                        }

                        $filteredProducts = $giftCardProduct;
                    }
                }

                if ($filteredProducts) {
                    $appliedProducts = WkGiftCardObj::getProductsPresenter($filteredProducts);

                    $appliedGiftCardsProduct = $this->filterProductsByPage($appliedProducts, $p, $n);

                    if ($appliedProducts) {
                        $this->pagination(count($appliedProducts));
                    }

                    $this->context->smarty->assign(
                        [
                            'products' => $appliedGiftCardsProduct,
                            'total_items' => count($filteredProducts),
                            'order_selected' => $order,
                            'staticPrices' => $this->getStaticProductPrice(),
                        ]
                    );

                    $productList = $this->context->smarty->fetch(_PS_MODULE_DIR_ . $this->module->name . '/views/templates/front/wk_gift_card_product.tpl');

                    exit(json_encode(['product_filter_tpl' => $productList]));
                }
            }
        }
    }

    /**
     * Get Static gift card price array
     *
     * @return bool || array
     */
    public function getStaticProductPrice()
    {
        $idShop = Context::getContext()->shop->id;
        $prices = WkGiftCardObj::getStaticGiftCardPrice($idShop);
        if ($prices && count($prices) > 0) {
            $prices = array_values(array_column($prices, 'amount'));
            $prices = array_unique($prices);
            $priceCurrencyWise = [];
            foreach ($prices as $price) {
                if ($price) {
                    $convertedAmount = Tools::convertPriceFull($price, null, $this->context->currency);
                    $priceCurrencyWise[] = [
                        'price' => $price,
                        'formated_price' => Tools::displayPrice($convertedAmount),
                    ];
                }
            }

            return $priceCurrencyWise;
        }

        return false;
    }
}
